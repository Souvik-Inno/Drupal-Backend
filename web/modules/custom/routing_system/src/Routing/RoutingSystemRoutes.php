<?php

/**
 * @file
 * Contains \Drupal\routing_system\Routing\RoutingSystemRoutes
 */

namespace Drupal\routing_system\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines the routes.
 */
class RoutingSystemRoutes {

  /**
   * Returns the routes with controller and permissions.
   * 
   * @return Route[]
   *   The routes.
   */
  public function routes() {
    $routes = [];
    $routes['routing_system.content'] = new Route(
      '/routing',
      [
        '_controller' => '\Drupal\routing_system\Controller\RouteController::content',
        '_title' => 'Routes'
      ],
      [
        '_permission' => 'Routing Permission',
      ]
    );
    return $routes;
  }
}
