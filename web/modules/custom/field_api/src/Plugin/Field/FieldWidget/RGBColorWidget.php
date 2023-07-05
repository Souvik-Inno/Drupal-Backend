<?php

namespace Drupal\field_api\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'rgb_color_widget' widget.
 *
 * @FieldWidget(
 *   id = "rgb_color_widget",
 *   label = @Translation("RGB Color Widget"),
 *   field_types = {
 *     "rgb_color"
 *   }
 * )
 */
class RGBColorWidget extends RGBWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, Array $element, Array &$form, FormStateInterface $form_state) {
    $element['value']['#type'] = 'color';
    $element['value']['#access'] = $this->checkAccess();
    return $element;
  }

}
