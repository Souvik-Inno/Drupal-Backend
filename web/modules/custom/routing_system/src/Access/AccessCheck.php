<?php

namespace Drupal\routing_system\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Checks access for displaying page.
 */
class AccessCheck {

  /**
   * The current user.
   * 
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new AccessCheck object.
   * 
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   */
  public function __construct(AccountInterface $account) {
    $this->currentUser = $account;
  }

  /**
   * A custom access check for Routing Permissions.
   * 
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */  
  public function access() {
    $current_user_roles = $this->currentUser->getRoles();
    // dump($current_user_roles);
    if ($this->currentUser->hasPermission('Routing Permission') || array_key_exists(1, $current_user_roles)) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }

}
