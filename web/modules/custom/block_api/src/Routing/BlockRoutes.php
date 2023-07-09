<?php

namespace Drupal\block_api\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines the routes.
 */
class BlockRoutes {

  /**
   * Returns the routes with controller and permissions.
   * 
   * @return Route[]
   *   The routes.
   */
  public function routes() {
    $routes = [];
    $routes['block_api.controller'] = new Route(
      '/block-api/controller',
      [
        '_controller' => '\Drupal\block_api\Controller\BlockApiController',
        '_title' => 'Controller'
      ],
      [
        '_permission' => 'access content',
      ]
    );
    $routes['block_api.content'] = new Route(
      '/custom-welcome-page',
      [
        '_controller' => '\Drupal\block_api\Controller\BlockApiController::customWelcome',
        '_title' => 'Custom Welcome Page',
      ],
      [
        '_permission' => 'access content',
      ]
    );
    $routes['block_api.logged_in'] = new Route(
      '/user/{id}?check_logged_in={status}',
      [
        '_controller' => '\Drupal\block_api\Controller\BlockApiController::getUserFromRoute',
        '_title' => 'Redirect',
      ],
      [
        '_permission' => 'access content',
      ]
    );

    return $routes;
  }

}
