<?php

namespace Drupal\hello_user\Controller;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class HelloUser
 * extends ControllerBase and contains functionality for module.
 */
class HelloUser extends ControllerBase {
  
  /**
   *  Function view to render content with account name with hello-user theme.
   * 
   *  @return array with $content.
   */
  public function view() {
    $content = [];
    $content['name'] = \Drupal::currentUser()->getAccountName();

    return [
      '#theme' => 'hello-user',
      '#content' => $content,
    ];
  }

}
