<?php

namespace Drupal\movie_entity\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Movie entity.
 *
 * @ContentEntityType(
 *   id = "movie_entity",
 *   label = @Translation("Movie Entity"),
 *   base_table = "movie_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   field_ui_base_route = "entity.movie_entity.settings",
 * )
 */
class MovieEntity extends ContentEntityBase implements ContentEntityInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the movie.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 255,
      ])
      ->setDisplayOptions('form', [
        'type' => 'textfield',
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ]);

    $fields['body'] = BaseFieldDefinition::create('text_with_summary')
      ->setLabel(t('Body'))
      ->setDescription(t('The body of the movie.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'text_editor',
        'settings' => [
          'rows' => 20,
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 1,
      ]);

    $fields['movie_price'] = BaseFieldDefinition::create('field_number')
      ->setLabel(t('Movie Price'))
      ->setDescription(t('The price of the movie.'))
      ->setRequired(true)
      ->setTranslatable(true)
      ->setSettings([
        'min' => 0,
        'max' => 1000,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 2,
      ]);

    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setDescription(t('The image of the movie.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'image_image',
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 3,
      ]);

    return $fields;
  }

}
