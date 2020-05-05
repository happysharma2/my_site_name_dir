<?php

namespace Drupal\ex_batch_drush9;

/**
 * Class BatchService.
 */
class BatchService {

  /**
   * Batch process callback.
   *
   * @param int $id
   *   Id of the batch.
   * @param string $operation_details
   *   Details of the operation.
   * @param object $context
   *   Context for operations.
   */
  public function processMyRecord($id, $operation_details, &$context) {

    // Simulate long process by waiting 100 microseconds.
    usleep(100);

    // Store some results for post-processing in the 'finished' callback.
    // The contents of 'results' will be available as $results in the
    // 'finished' function (in this example, batch_example_finished()).
    $context['results'][] = $id;

    //$articleStatsEndPoint = getenv('ARTICLE_STATS_END_POINT');
    //$articleStatsApiUsername = getenv('ARTICLE_STATS_API_USERNAME');
    //$articleStatsApiPaswd = getenv('ARTICLE_STATS_API_PASSWORD');

    $articleStatsEndPoint = 'https://viewsapi.hindawi.com/';
    $articleStatsApiUsername = 'NewWebsite';
    $articleStatsApiPaswd = 'NewWebsite54321';

    $views = $pdfdownloads = $epubdownloads = $citations = 0;
    if (!empty($articleStatsEndPoint) && !empty($articleStatsApiUsername) && !empty($articleStatsApiPaswd)) {
      $client = \Drupal::httpClient();
      $database = \Drupal::database();

      $article_id = $id
      if (!empty($article_id)) {
        // Getting the API response
        $request = $client->get($articleStatsEndPoint . "api/getcounts/$article_id", [
          'auth' => [$articleStatsApiUsername, $articleStatsApiPaswd]
        ]);
        $response = json_decode($request->getBody());
        if (!empty($response[0])) {
          if (isset($response[0]->views)) {
            $views = $response[0]->views;
          }
          if (isset($response[0]->pdfdownloads)) {
            $pdfdownloads = $response[0]->pdfdownloads;
          }
          if (isset($response[0]->epubdownloads)) {
            $epubdownloads = $response[0]->epubdownloads;
          }
          if (isset($response[0]->Citations)) {
            $citations = $response[0]->Citations;
          }
        }
        $results[] = $database->insert('hindawi_article_stats')
          ->fields([
            'article_id' => $article_id,
            'views' => $views,
            'pdfdownloads' => $pdfdownloads,
            'citations' => $citations,
            'epubdownloads' => $epubdownloads,
          ])->execute();

    // Optional message displayed under the progressbar.
    $context['message'] = t('Running Batch "@id" @details',
      ['@id' => $id, '@details' => $operation_details]
    );

  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array $results
   *   Array of results for post processing.
   * @param array $operations
   *   Array of operations.
   */
  public function processMyRecordFinished($success, array $results, array $operations) {
    $messenger = \Drupal::messenger();
    if ($success) {
      // Here we could do something meaningful with the results.
      // We just display the number of nodes we processed...
      $messenger->addMessage(t('@count results processed.', ['@count' => count($results)]));
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $messenger->addMessage(
        t('An error occurred while processing @operation with arguments : @args',
          [
            '@operation' => $error_operation[0],
            '@args' => print_r($error_operation[0], TRUE),
          ]
        )
      );
    }
  }

}
