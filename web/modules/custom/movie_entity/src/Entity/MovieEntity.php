<?php

namespace Drupal\movie_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\movie_entity\MovieEntityInterface;

/**
 * Defines the movie entity entity type.
 *
 * @ConfigEntityType(
 *   id = "movie_entity",
 *   label = @Translation("Movie Entity"),
 *   label_collection = @Translation("Movie Entities"),
 *   label_singular = @Translation("movie entity"),
 *   label_plural = @Translation("movie entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count movie entity",
 *     plural = "@count movie entities",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\movie_entity\MovieEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\movie_entity\Form\MovieEntityForm",
 *       "edit" = "Drupal\movie_entity\Form\MovieEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *   },
 *   config_prefix = "movie_entity",
 *   admin_permission = "administer movie_entity",
 *   links = {
 *     "collection" = "/admin/structure/movie-entity",
 *     "add-form" = "/admin/structure/movie-entity/add",
 *     "edit-form" = "/admin/structure/movie-entity/{movie_entity}",
 *     "delete-form" = "/admin/structure/movie-entity/{movie_entity}/delete",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "year",
 *     "movie",
 *   },
 * )
 */
final class MovieEntity extends ConfigEntityBase implements MovieEntityInterface {

  /**
   * The movie ID.
   *
   * @var string
   */
  protected string $id;

  /**
   * The movie label.
   *
   * @var string
   */
  protected string $label;

  /**
   * The movie description.
   *
   * @var string
   */
  protected string $description;

  /**
   * The release year.
   *
   * @var int
   */
  protected $year;

  /**
   * The referenced movie.
   * 
   * @var string
   */
  protected $movie;

  /**
   * {@inheritDoc}
   */
  public function getYear() {
    return $this->year;
  }

  /**
   * {@inheritDoc}
   */
  public function setYear($year) {
    $this->year = $year;
  }

  /**
   * {@inheritDoc}
   */
  public function getMovie() {
    return $this->movie;
  }

  /**
   * {@inheritDoc}
   */
  public function setMovie($movie) {
    $this->movie = $movie;
  }

}
