<?php

/**
 * @file
 * Hooks provided by the onesignal module.
 */

/**
 * @addtogroup hooks
 * @{
 */

use Drupal\Core\Access\AccessResult;

/**
 * Control onesignal access.
 *
 * Modules may implement this hook if they want to disable onesignal for some
 * reasons.
 *
 * @return \Drupal\Core\Access\AccessResultInterface|bool|null
 *   - ONESIGNAL_ACCESS_ALLOW: If onesignal is allowed.
 *   - ONESIGNAL_ACCESS_DENY: If onesignal is disabled.
 *   - ONESIGNAL_ACCESS_IGNORE: If onesignal check is.
 *
 * @ingroup node_access
 */
function hook_onesignal_access() {
  // Disable for frontpage.
  if (\Drupal::service('path.matcher')->isFrontPage()) {
    return AccessResult::forbidden();
  }
  return AccessResult::neutral();
}

/**
 * Alter results of onesignal access check results.
 */
function hook_onesignal_access_alter(&$results) {
  // Force disable for frontpage.
  if (\Drupal::service('path.matcher')->isFrontPage()) {
    $result = AccessResult::forbidden();
  }
  else {
    $result = AccessResult::neutral();
  }
  $results['my_module_check'] = $result;
}

/**
 * @} End of "addtogroup hooks".
 */
