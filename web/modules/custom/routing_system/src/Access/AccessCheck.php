<?php

namespace Drupal\routing_system\Access;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Checks access for displaying page.
 */
class AccessCheck extends RouteSubscriberBase {

  /**
   * {@inheritDoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('routing_system.content')) {
      $route->setPath('/changed');
      $route->setRequirement('_role', 'administrator');
    }
  }

}
