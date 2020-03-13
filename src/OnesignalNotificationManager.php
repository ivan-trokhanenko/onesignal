<?php

namespace Drupal\onesignal;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\onesignal\Config\ConfigManagerInterface;

/**
 * Class OnesignalNotificationManager
 *
 * @package Drupal\onesignal
 */
class OnesignalNotificationManager implements OnesignalNotificationManagerInterface {
  
  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;
  
  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;
  
  /**
   * The OneSignal config manager service.
   *
   * @var \Drupal\onesignal\Config\ConfigManagerInterface
   */
  private $configManager;
  
  /**
   * OnesignalNotificationManager constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The Guzzle Http Client.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\onesignal\Config\ConfigManagerInterface
   *   The config manager service.
   */
  public function __construct(ClientInterface $client, MessengerInterface $messenger, ConfigManagerInterface $configManager) {
    $this->httpClient = $client;
    $this->messenger = $messenger;
    $this->configManager = $configManager;
  }
  
  /**
   * {@inheritdoc}
   */
  public function allSubscribersSend(array $param) {
    try {
      // Title.
      $headings = [
        'en' => $param['title']
      ];
      // Body.
      $content = [
        'en' => $param['summary']
      ];
      $fields = [
        'app_id' => $this->configManager->getAppId(),
        'included_segments' => ['All'],
        'headings' =>$headings,
        'contents' => $content,
        'url' => $param['url'],
        'big_picture' => $param['img_url'],
        'chrome_big_picture' => $param['img_url'],
        'chrome_web_image' => $param['img_url'],
        'chrome_web_icon' => $this->getIconUrl(),
        'chrome_web_badge' => $this->getIconUrl(),
        'chrome_icon' => $this->getIconUrl(),
        'large_icon' => $this->getIconUrl()
      ];
      $fields = json_encode($fields);
      // Send data to onesignal.
      // TODO. Rewrite using Drupal HTTP client.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, self::API_LINK);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $this->configManager->getRestApiKey()
      ]);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $response = curl_exec($ch);
      curl_close($ch);
      $return["allresponses"] = $response;
      $return = json_encode($return);
      $this->messenger->addMessage('Notification created');
      return $response;
    }
    catch (\Exception $e) {
      $this->messenger->addError('Notification not created');
      exit(1);
    }
  }
  
}
