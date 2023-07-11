<?php

namespace Drupal\hello_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for hello user content routes.
 */
class HelloUser extends ControllerBase {
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs new HelloUser controller object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user service.
   */
  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
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
   * Renders a page to view user name.
   *
   * @return array
   *   An array suitable for showing content.
   */
  public function view() {
    return [
      '#theme' => 'hello_user',
      '#content' => $this->currentUser->getDisplayName(),
      '#cache' => [
        'tags' => ['user:' . $this->currentUser->id()],
      ],
    ];
  }

}
