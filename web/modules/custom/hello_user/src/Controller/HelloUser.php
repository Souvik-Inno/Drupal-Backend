<?php

namespace Drupal\hello_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hello_user\CurrentUser;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for hello user content routes.
 */
class HelloUser extends ControllerBase {
  /**
   * The current user.
   * 
   * @var \Drupal\hello_user\CurrentUser
   */
  protected $currentUser;

  /**
   * Constructs new HelloUser controller object.
   * 
   * @param \Drupal\hello_user\CurrentUser $currentUser
   *   The current user service.
   */
  public function __construct(CurrentUser $currentUser) {
    $this->currentUser = $currentUser;
  }
  
  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('hello_user.current_user'),
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
      '#content' => $this->currentUser->getUserDisplayName(),
      '#cache' => [
        'tags' => $this->currentUser->getUserCacheTags(),
      ],
    ];
  }

}
