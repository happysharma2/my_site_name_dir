<?php

namespace Drupal\image_canvas_editor_api\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Class ImageCanvasEditor.
 *
 * @see \Drupal\image_canvas_editor_api\Plugin\EditorPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class ImageCanvasEditor extends Plugin {

  /**
   * The plugin id.
   *
   * @var string
   */
  public $id;

  /**
   * The label.
   *
   * @var string
   */
  public $label;

}
