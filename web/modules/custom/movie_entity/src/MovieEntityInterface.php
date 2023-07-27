<?php

namespace Drupal\movie_entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a movie entity entity type.
 */
interface MovieEntityInterface extends ConfigEntityInterface {

  /**
   * Returns the release year.
   *
   * @return int
   *   The release year.
   */
  public function getYear();

  /**
   * Sets the release year.
   *
   * @param int $year
   *   The release year.
   */
  public function setYear($year);

  /**
   * Gets the referenced movie.
   * 
   * @return string
   *   The movie referenced.
   */
  public function getMovie();

  /**
   * Sets the referenced movie.
   * 
   * @param string $movie
   *   The movie referenced.
   */
  public function setMovie($movie);

}
