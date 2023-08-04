<?php

namespace Drupal\movie_entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\movie_entity\Entity\MovieEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Movie Entity form.
 */
final class MovieEntityForm extends EntityForm {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new MovieEntityForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {

    $form = parent::form($form, $form_state);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => [MovieEntity::class, 'load'],
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->entity->status(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $this->entity->get('description'),
    ];

    $form['year'] = [
      '#type' => 'number',
      '#title' => $this->t('Year'),
      '#default_value' => $this->entity->get('year'),
      '#required' => TRUE,
    ];

    // dd($this->entity->get('movie'));

    $form['movie'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Movie'),
      '#target_type' => 'node',
      // '#tags' => TRUE,
      '#default_value' => $this->entity->get('movie') ? $this->entityManager->getStorage('node')->load($this->entity->get('movie')) : "",
      '#required' => TRUE,
      '#selection_settings' => [
        'target_bundles' => ['movie'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);
    $message_args = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        \SAVED_NEW => $this->t('Created new movie %label.', $message_args),
        \SAVED_UPDATED => $this->t('Updated movie %label.', $message_args),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

}
