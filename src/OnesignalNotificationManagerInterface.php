<?php

namespace Drupal\onesignal;

/**
 * Provides interface for OnesignalNotificationManager.
 */
interface OnesignalNotificationManagerInterface {
  
  /**
   * The OneSignal API link.
   *
   * @var string
   */
  const API_LINK = 'https://onesignal.com/api/v1/notifications';
  
  /**
   * Create and send notification to all subscribers.
   *
   * @param array $param
   *
   * @return mixed
   *
   * @see https://documentation.onesignal.com/reference#section-example-code-create-notification
   */
  public function allSubscribersSend(array $param);
  
}
