<?php

namespace Drupal\field_api\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'rgb_color_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "rgb_color_formatter",
 *   label = @Translation("RGB Color Formatter"),
 *   field_types = {
 *     "rgb_color"
 *   }
 * )
 */
class RGBColorFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('The content area color has been changed to @code', ['@code' => $item->value]),
        '#attributes' => [
          'style' => 'background-color: ' . $item->value . ';',
        ],
      ];
    }
    return $elements;
  }

}
