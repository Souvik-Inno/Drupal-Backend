<?php

namespace Drupal\database_api\Controller;

use Drupal\Core\Controller\ControllerBase;

class EventsController extends ControllerBase {
  
  public function listing() {
    $build = [];
    $build['listing'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Hello User to Events listing'),
    ];
    return $build;
  }
}
