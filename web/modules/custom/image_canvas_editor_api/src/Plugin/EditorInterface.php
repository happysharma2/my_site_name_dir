<?php

namespace Drupal\image_canvas_editor_api\Plugin;

/**
 * Editor interface.
 */
interface EditorInterface {

  /**
   * Should dictate how the editor should be rendered.
   *
   * @return array
   *   A render array.
   */
  public function renderEditor($image_url);

}
