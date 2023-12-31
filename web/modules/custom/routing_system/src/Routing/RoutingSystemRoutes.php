<?php

namespace Drupal\routing_system\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines the routes.
 */
class RoutingSystemRoutes {

  /**
   * Returns the routes with controller and permissions.
   *
   * @return \Symfony\Component\Routing\Route[]
   *   The routes.
   */
  public function routes() {
    $routes = [];
    $routes['routing_system.content'] = new Route(
      '/routing',
      [
        '_controller' => '\Drupal\routing_system\Controller\RouteController::content',
        '_title' => 'Routes',
      ],
      [
        '_permission' => 'Routing Permission',
      ],
    );
    $routes['routing_system.dynamic_content'] = new Route(
      '/campaign/value/{value}',
      [
        '_controller' => '\Drupal\routing_system\Controller\RouteController::dynamicContent',
        '_title' => 'Dynamic Route',
      ],
      [
        '_permission' => 'access content',
      ],
    );
    return $routes;
  }

}
