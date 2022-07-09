<?php

namespace Drupal\custom_site_settings\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Determines access to css.
 */
class CssAccessCheck implements AccessInterface {

  public function access(AccountInterface $account) {
    if ($account->id() == 1) {
      return AccessResult::forbidden();
    } else {
      return AccessResult::allowed();
    }    
  }
}