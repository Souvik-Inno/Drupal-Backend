<?php

namespace Drupal\routing_system\Access;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Checks access for displaying page.
 */
class AccessCheck extends RouteSubscriberBase {

  /**
   * Alters route and sets requirements.
   * 
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The collection of routes to alter.
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('routing_system.content')) {
      $route->setRequirement('_role', 'administrator');
    }
  }

}
