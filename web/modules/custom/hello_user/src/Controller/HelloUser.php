<?php

namespace Drupal\hello_user\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for hello user content routes.
 */
class HelloUser extends ControllerBase {

  /**
   * Renders a page to view user name.
   *
   * @return array
   *   An array suitable for showing content.
   */
  public function view() {
    return [
      '#theme' => 'hello_user',
      '#content' => \Drupal::currentUser()->getDisplayName(),
      '#cache' => [
        'tags' => ["user:" . \Drupal::currentUser()->id()],
      ],
    ];
  }

}
