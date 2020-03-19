<?php

namespace Drupal\onesignal\Config;

use Drupal\Core\Config\ConfigFactory;

/**
 * Manages Onesignal module configuration.
 *
 * @package Drupal\onesignal\Config
 */
class ConfigManager implements ConfigManagerInterface {

  const PAGES = "/admin\n/admin/*\n/batch\n/node/add*\n/node/*/*\n/user/*/*";

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Settings array.
   *
   * @var array
   */
  protected $settings;

  /**
   * ConfigManager constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   The config factory service.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->config = $configFactory->getEditable('onesignal.config');
//    $this->config = $configFactory->get('onesignal.settings');
    $this->getSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function getAppId() {
    return $this->config->get('onesignal_app_id');
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginalAppId() {
    return $this->config->getOriginal('onesignal_app_id', FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getSafariWebId() {
    return $this->config->get('onesignal_safari_web_id');
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginalSafariWebId() {
    return $this->config->getOriginal('onesignal_safari_web_id', FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getRestApiKey() {
    return $this->config->get('onesignal_rest_api_key');
  }

  /**
   * {@inheritdoc}
   */
  public function getAutoRegister() {
    return $this->config->get('onesignal_auto_register');
  }

  /**
   * {@inheritdoc}
   */
  public function getNotifyButton() {
    return $this->config->get('onesignal_notify_button');
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalhostSecure() {
    return $this->config->get('onesignal_localhost_secure');
  }

  /**
   * {@inheritdoc}
   */
  public function getActionMessage() {
    return $this->config->get('onesignal_action_message');
  }

  /**
   * {@inheritdoc}
   */
  public function getAcceptButtonText() {
    return $this->config->get('onesignal_accept_button');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelButtonText() {
    return $this->config->get('onesignal_cancel_button');
  }

  /**
   * {@inheritdoc}
   */
  public function getWelcomeTitle() {
    return $this->config->get('onesignal_welcome_title');
  }

  /**
   * {@inheritdoc}
   */
  public function getWelcomeMessage() {
    return $this->config->get('onesignal_welcome_message');
  }

  /**
   * {@inheritdoc}
   */
  public function getSettings() {
    if (!$this->settings) {
      $settings = (array) $this->config->getOriginal();
      $settings += [
        'onesignal_app_id' => NULL,
        'onesignal_safari_web_id' => NULL,
        'onesignal_rest_api_key' => NULL,
        'onesignal_auto_register' => NULL,
        'onesignal_notify_button' => NULL,
        'onesignal_localhost_secure' => NULL,
        'onesignal_action_message' => NULL,
        'onesignal_accept_button' => NULL,
        'onesignal_cancel_button' => NULL,
        'onesignal_welcome_title' => NULL,
        'onesignal_welcome_message' => NULL,
        'visibility_pages' => 0,
        'pages' => static::PAGES,
        'visibility_roles' => 0,
        'roles' => [],
      ];
      $this->settings = $settings;
    }
    return $this->settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getSetting($key, $default = NULL) {
    $this->getSettings();
    return array_key_exists($key, $this->settings) ? $this->settings[$key] : $default;
  }

}
