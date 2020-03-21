<?php

namespace Drupal\onesignal;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\onesignal\Config\ConfigManagerInterface;

/**
 * Class OnesignalAccess.
 *
 * @package Drupal\onesignal
 */
class OnesignalAccess implements OnesignalAccessInterface {

  const ONESIGNAL_ACCESS_ALLOW = TRUE;
  const ONESIGNAL_ACCESS_DENY = FALSE;
  const ONESIGNAL_ACCESS_IGNORE = NULL;

  /**
   * OneSignal settings.
   *
   * @var \Drupal\onesignal\Config\ConfigManagerInterface
   */
  protected $settings;

  /**
   * Module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * Alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * Path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Page match.
   *
   * @var bool
   */
  protected $pageMatch;

  /**
   * OnesignalAccess constructor.
   *
   * @param \Drupal\onesignal\Config\ConfigManagerInterface $onesignal_settings
   *   OneSignal settings.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   Current path.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   Alias manager.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   Path matcher.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   */
  public function __construct(
    ConfigManagerInterface $onesignal_settings,
    ModuleHandlerInterface $module_handler,
    ConfigFactoryInterface $config_factory,
    CurrentPathStack $current_path,
    AliasManagerInterface $alias_manager,
    PathMatcherInterface $path_matcher,
    AccountInterface $current_user
  ) {
    $this->settings = $onesignal_settings;
    $this->moduleHandler = $module_handler;
    $this->configFactory = $config_factory;
    $this->currentPath = $current_path;
    $this->aliasManager = $alias_manager;
    $this->pathMatcher = $path_matcher;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function check() {
    if (!$this->settings->getAppId() || !$this->settings->getSetting('enabled')) {
      return FALSE;
    }

    $result = AccessResult::neutral()
      ->andIf($this->pathCheckResult())
      ->andIf($this->roleCheck());

    $access = [];
    foreach ($this->moduleHandler->getImplementations('onesignal_access') as $module) {
      $module_result = $this->moduleHandler->invoke($module, 'onesignal_access');
      if (is_bool($module_result)) {
        $access[$module] = $module_result;
      }
      elseif ($module_result instanceof AccessResult) {
        $access[$module] = !$module_result->isForbidden();
      }
    }

    $this->moduleHandler->alter('onesignal_access', $access);

    foreach ($access as $module_result) {
      if (is_bool($module_result)) {
        $result = $result->andIf(AccessResult::forbiddenIf(!$module_result));
      }
      elseif ($module_result instanceof AccessResult) {
        $result = $result->andIf($module_result);
      }
    }

    return !$result->isForbidden();
  }

  /**
   * Check path.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Path result.
   */
  protected function pathCheckResult() {
    if (!isset($this->pageMatch)) {
      $visibility = $this->settings->getSetting('visibility_pages');
      $setting_pages = $this->settings->getSetting('pages');

      if (!$setting_pages) {
        $this->pageMatch = TRUE;
        return AccessResult::allowed();
      }

      $pages = mb_strtolower($setting_pages);
      if ($visibility < 2) {
        $path = $this->currentPath->getPath();
        $path_alias = mb_strtolower($this->aliasManager->getAliasByPath($path));
        $path_match = $this->pathMatcher->matchPath($path_alias, $pages);
        $alias_match = (($path != $path_alias) && $this->pathMatcher->matchPath($path, $pages));
        $this->pageMatch = $path_match || $alias_match;

        // When $visibility has a value of 0, the subscription popup is
        // displayed on all pages except those listed in $pages. When set to 1,
        // it is displayed only on those pages listed in $pages.
        $this->pageMatch = !($visibility xor $this->pageMatch);
      }
      else {
        $this->pageMatch = FALSE;
      }
    }

    return AccessResult::forbiddenIf(!$this->pageMatch);
  }

  /**
   * Check user.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   User result.
   */
  protected function roleCheck() {
    $visibility = $this->settings->getSetting('visibility_roles');
    $enabled = $visibility;
    $roles = $this->settings->getSetting('roles');

    // The $roles stores the selected roles as an array where
    // the keys are the role IDs. When the role is not selected the
    // value is 0. If a role is selected the value is the role ID.
    $checked_roles = array_filter($roles);
    if (empty($checked_roles)) {
      // No role is selected for OneSignal
      // therefore all roles will see subscription popup.
      return AccessResult::allowed();
    }

    if (count(array_intersect($this->currentUser->getRoles(), $checked_roles))) {
      $enabled = !$visibility;
    }

    return AccessResult::forbiddenIf(!$enabled);
  }

}
