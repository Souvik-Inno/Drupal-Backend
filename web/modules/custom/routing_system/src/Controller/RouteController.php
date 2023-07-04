<?php

/**
 * @file
 * Contains \Drupal\routing_system\Controller\RouteController
 */

namespace Drupal\routing_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\routing_system\Access\AccessCheck;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for routing system.
 */
class RouteController extends ControllerBase {

  /**
   * Checks if the user has access.
   * 
   * @var \Drupal\routing_system\Access\AccessCheck
   */
  protected $checkUser;

  /**
   * Creates a new AccessCheck object.
   * 
   * @param \Drupal\routing_system\Access\AccessCheck $currentUser
   *   The current user.
   */
  public function __construct(AccessCheck $currentUser) {
    $this->checkUser = $currentUser;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('routing_system.access_checker'),
    );
  }

  /**
   * Returns a render-able array for a page.
   * 
   * @return array
   *   Array to render page.
   */
  public function content() {
    return [
      '#markup' => $this->t('You have a granted access to the page.'),
    ];
  }

  /**
   * Renders a markup to show the value of the parameter.
   * 
   * @return array
   *   Array to render page.
   */
  public function dynamicContent($value) {
    return [
      '#markup' => $this->t('The value of the parameter is @value', ['@value' => $value]),
    ];
  }

}
