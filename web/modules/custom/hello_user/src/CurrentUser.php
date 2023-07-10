<?php

namespace Drupal\hello_user;

use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a service to get current user's display name.
 */
class CurrentUser {

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
   * Gets the current user's display name.
   *
   * @return string
   *   The display name.
   */
  public function getUserDisplayName() {
    return $this->currentUser->getDisplayName();
  }

  /**
   * Gets the cache tag for user.
   *
   * @return string[]
   *   The cache tag.
   */
  public function getUserCacheTags() {
    $cacheTags = ['user:' . $this->currentUser->id()];
    return $cacheTags;
  }

}
