<?php

namespace Drupal\movie_entity\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete Movie Awards entities.
 */
class MovieAwardsConfigDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the Movie Awards for year %year?', [
      '%year' => $this->entity->getYear(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.movie_entity.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    // drupal_set_message($this->t('The Movie Awards for year %year has been deleted.', [
    //   '%year' => $this->entity->getYear(),
    // ]));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
