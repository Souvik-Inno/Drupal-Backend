<?php

namespace Drupal\routing_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for routing system.
 */
class RouteController extends ControllerBase {

  /**
   * The current user.
   *
   * @var \Drupal\routing_system\Access\AccessCheck
   */
  protected $currentUser;

  /**
   * Creates a new AccessCheck object.
   *
   * @param \Drupal\routing_system\Access\AccessCheck $account
   *   The current user.
   */
  public function __construct(AccountInterface $account) {
    $this->currentUser = $account;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
    );
  }

  /**
   * Returns a render-able array for a page.
   *
   * @return array|Response
   *   Array to render page if access granted.
   */
  public function content() {
    if ($this->currentUser->hasPermission('Routing Permission')) {
      return [
        '#markup' => $this->t('You have a granted access to the page.'),
      ];
    }
    return new Response('Access Denied', 403);
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
