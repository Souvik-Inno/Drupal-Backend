<?php

namespace Drupal\field_api\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'rgb_color_widget' widget.
 *
 * @FieldWidget(
 *   id = "hex3_color_widget",
 *   label = @Translation("RGB Hex 3 Color Widget"),
 *   field_types = {
 *     "rgb_color"
 *   }
 * )
 */
class Hex3ColorWidget extends RGBWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = $items[$delta]->value ?? '';
    if (!empty($value)) {
      preg_match_all('@..@', substr($value, 1), $match);
    }
    else {
      $match = [[]];
    }
    $element += [
      '#type' => 'details',
      '#element_validate' => [
      [$this, 'validate'],
      ],
      '#access' => $this->checkAccess(),
    ];
    foreach ([
      'r' => $this->t('Red'),
      'g' => $this->t('Green'),
      'b' => $this->t('Blue'),
    ] as $key => $title) {
      $element[$key] = [
        '#type' => 'textfield',
        '#title' => $title,
        '#size' => 2,
        '#default_value' => array_shift($match[0]),
        '#attributes' => ['class' => ['rgb-entry']],
        '#description' => $this->t('The 2-digit hexadecimal representation of @color saturation, like "a1" or "ff"', ['@color' => $title]),
      ];
      if ($element['#required']) {
        $element[$key]['#required'] = TRUE;
      }
    }
    return ['value' => $element];
  }

  /**
   * Validate the fields and convert them into a single value as text.
   */
  public function validate($element, FormStateInterface $form_state) {
    $values = [];
    foreach (['r', 'g', 'b'] as $colorfield) {
      $values[$colorfield] = $element[$colorfield]['#value'];
      if (strlen($values[$colorfield]) == 0) {
        $form_state->setValueForElement($element, '');
        return;
      }
      if ((strlen($values[$colorfield]) != 2) || !ctype_xdigit($values[$colorfield])) {
        $form_state->setError($element[$colorfield], $this->t("Saturation value must be a 2-digit hexadecimal value between 00 and ff."));
      }
    }
    $value = strtolower(sprintf('#%02s%02s%02s', $values['r'], $values['g'], $values['b']));
    $form_state->setValueForElement($element, $value);
  }

}
