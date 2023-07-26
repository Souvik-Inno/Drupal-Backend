<?php

namespace Drupal\movie_entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Movie Awards edit forms.
 *
 * @ingroup movie_entity
 */
class MovieAwardsConfigForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $movie_entity = $this->entity;
    $form['year'] = [
      '#type' => 'number',
      '#title' => $this->t('Year'),
      '#default_value' => $movie_entity->getYear(),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $movie_entity = $this->entity;
    $status = $movie_entity->save();

    if ($status) {
      // drupal_set_message($this->t('Saved the %label Movie Awards.', [
      //   '%label' => $movie_entity->getYear(),
      // ]));
    }
    else {
      // drupal_set_message($this->t('The %label Movie Awards was not saved.', [
      //   '%label' => $movie_entity->getYear(),
      // ]), 'error');
    }

    $form_state->setRedirectUrl($movie_entity->toUrl('collection'));
  }

}
