<?php

namespace Drupal\onesignal;

/**
 * Interface OnesignalAccessInterface.
 *
 * @package Drupal\onesignal
 */
interface OnesignalAccessInterface {

  /**
   * Determines where we display subscription popup.
   *
   * @return bool
   *   Return TRUE if user can view popup.
   */
  public function check();

}
