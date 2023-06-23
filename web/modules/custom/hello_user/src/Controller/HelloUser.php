<?php

namespace Drupal\hello_user\Controller;
use Drupal\Core\Controller\ControllerBase;

class HelloUser extends ControllerBase {
  
  public function view() {
    $content = [];
    $content['name'] = \Drupal::currentUser()->getAccountName();

    return [
      '#theme' => 'hello-user',
      '#content' => $content,
    ];
  }

}