<?php

namespace Drupal\block_api;

use Drupal\Core\Session\AccountProxyInterface;

/**
 * Gets User's Role.
 */
class UserRole {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new CurrentUser object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user service.
   */
  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * Gets the current user's role.
   *
   * @return \Drupal\user\Entity\Role
   *   The user's role.
   */
  public function getUserRole() {
    $roles = $this->currentUser->getRoles();
    return $roles[array_key_last($roles)];
  }

  /**
   * Gets the cache tag for user.
   *
   * @return string[]
   *   The cache tag.
   */
  public function getUserCacheTags() {
    return ['user:' . $this->currentUser->id()];
  }

}
