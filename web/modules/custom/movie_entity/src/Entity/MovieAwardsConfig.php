<?php

namespace Drupal\movie_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Movie Awards configuration entity.
 *
 * @ConfigEntityType(
 *   id = "movie_entity",
 *   label = @Translation("Movie Awards"),
 *   handlers = {
 *     "list_builder" = "Drupal\movie_entity\MovieAwardsConfigListBuilder",
 *     "form" = {
 *       "add" = "Drupal\movie_entity\Form\MovieAwardsConfigForm",
 *       "edit" = "Drupal\movie_entity\Form\MovieAwardsConfigForm",
 *       "delete" = "Drupal\movie_entity\Form\MovieAwardsConfigDeleteForm",
 *     }
 *   },
 *   config_prefix = "movie_entity",
 *   admin_permission = "administer movie awards",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "year",
 *   },
 *   links = {
 *     "collection" = "/admin/structure/movie-awards",
 *     "edit-form" = "/admin/structure/movie-awards/{movie_entity}/edit",
 *     "delete-form" = "/admin/structure/movie-awards/{movie_entity}/delete",
 *   }
 * )
 */
class MovieAwardsConfig extends ConfigEntityBase {

  /**
   * The movie awards ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The year of the movie awards.
   *
   * @var int
   */
  protected $year;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * Get the year of the movie awards.
   *
   * @return int
   *   The year of the movie awards.
   */
  public function getYear() {
    return $this->year;
  }

  /**
   * Set the year of the movie awards.
   *
   * @param int $year
   *   The year of the movie awards.
   */
  public function setYear($year) {
    $this->year = $year;
  }

}
