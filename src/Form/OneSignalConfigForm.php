<?php

namespace Drupal\onesignal\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\onesignal\Config\ConfigManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OneSignalConfigForm.
 */
class OneSignalConfigForm extends ConfigFormBase {
  
  /**
   * The config manager service.
   *
   * @var \Drupal\onesignal\Config\ConfigManagerInterface
   */
  private $configManager;
  
  /**
   * OneSignalConfigForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The factory for configuration objects.
   * @param \Drupal\onesignal\Config\ConfigManagerInterface $configManager
   *   The config manager service.
   */
  public function __construct(ConfigFactoryInterface $configFactory, ConfigManagerInterface $configManager) {
    parent::__construct($configFactory);
    $this->configManager = $configManager;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('onesignal.config_manager')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['onesignal.config'];
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'one_signal_config_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['onesignal_app_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OneSignal App ID'),
      '#description' => $this->t('Find it at https://OneSignal.com under your app Settings &gt; Keys &amp; IDs.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getAppId(),
    ];
    // Disable the field if its value has been set in the settings.php file.
    // This is useful when there are different configurations for development,
    // testing and live environments.
    if (!empty($this->configManager->getAppId()) && ($this->configManager->getOriginalAppId() != $this->configManager->getAppId())) {
      $form['onesignal_app_id']['#disabled'] = TRUE;
      $form['onesignal_app_id']['#description'] = $this->t('This field has been disabled because its value is being overridden in the settings.php file.');
    }
    $form['onesignal_safari_web_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OneSignal Safari App ID'),
      '#description' => $this->t('Find it at https://OneSignal.com under your app Settings &gt; Platforms &gt; Apple Safari.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getSafariWebId(),
    ];
    // Disable the field if its value has been set in the settings.php file
    // This is useful when there are different configurations for development,
    // testing and live environments.
    if (!empty($this->configManager->getSafariWebId()) && ($this->configManager->getOriginalSafariWebId() != $this->configManager->getSafariWebId())) {
      $form['onesignal_safari_web_id']['#disabled'] = TRUE;
      $form['onesignal_safari_web_id']['#description'] = $this->t('This field has been disabled because its value is being overridden in the settings.php file.');
    }
    $form['onesignal_rest_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OneSignal REST API key'),
      '#description' => $this->t('REST API key.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getRestApiKey(),
    ];
    $form['onesignal_auto_register'] = [
      '#type' => 'select',
      '#title' => $this->t('Auto register'),
      '#description' => $this->t('Set to true to automatically prompt visitors to accept notifications.'),
      '#options' => [
        0 => $this->t('Unset'),
        'true' => $this->t('True'),
        'false' => $this->t('False'),
      ],
      '#default_value' => $this->configManager->getAutoRegister(),
    ];
    $form['onesignal_notify_button'] = [
      '#type' => 'select',
      '#title' => $this->t('Notify button visibility'),
      '#description' => $this->t('True will make the Bell visible if it has been configured at OneSignal.'),
      '#options' => [
        0 => $this->t('Unset'),
        'true' => $this->t('True'),
        'false' => $this->t('False'),
      ],
      '#default_value' => $this->configManager->getNotifyButton(),
    ];
    $form['onesignal_localhost_secure'] = [
      '#type' => 'select',
      '#title' => $this->t('Localhost secure origin'),
      '#description' => $this->t('Development setting. True allows a Locahost behave as if it had SSL.'),
      '#options' => [
        0 => $this->t('Unset'),
        'true' => $this->t('True'),
        'false' => $this->t('False'),
      ],
      '#default_value' => $this->configManager->getLocalhostSecure(),
    ];
    $form['onesignal_prompt'] = [
      '#type' => 'details',
      '#title' => $this->t('Prompt settings'),
      '#description' => $this->t('Controls the box that prompts the user to receive notifications. Leave blank to use what comes from OneSignal.'),
      '#open' => FALSE,
    ];
    // If empty get the default message from install/onesignal.settings.yml
    $form['onesignal_action_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Action Message'),
      '#description' => $this->t('Text of the invitation to signup for both the HTTP prompt and the browser popup.'),
      '#maxlength' => 90,
      '#size' => 64,
      '#default_value' => $this->configManager->getActionMessage(),
      '#group' => 'onesignal_prompt',
    ];
    // If empty get the default message from install/onesignal.settings.yml
    $form['onesignal_accept_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Accept Button'),
      '#description' => $this->t('Text of the Accept button.'),
      '#maxlength' => 15,
      '#size' => 15,
      '#default_value' => $this->configManager->getAcceptButtonText(),
      '#group' => 'onesignal_prompt',
    ];
    // If empty get the default message from install/onesignal.settings.yml
    $form['onesignal_cancel_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cancel Button'),
      '#description' => $this->t('Text of the Cancel button.'),
      '#maxlength' => 15,
      '#size' => 15,
      '#default_value' => $this->configManager->getCancelButtonText() ,
      '#group' => 'onesignal_prompt',
    ];
    $form['onesignal_welcome'] = [
      '#type' => 'details',
      '#title' => $this->t('Welcome settings'),
      '#description' => $this->t('Controls the first message sent confirming the sign up. Leave blank to use what comes from OneSignal.'),
      '#open' => FALSE,
    ];
    // If the title is not set we use the site name.
    $form['onesignal_welcome_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Welcome Notification Title'),
      '#description' => $this->t('Title of the first notification the user receives confirming the enrollment to receive notifications'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getWelcomeTitle(),
      '#group' => 'onesignal_welcome',
    ];
    // If empty get the default message from install/onesignal.settings.yml
    $form['onesignal_welcome_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Welcome Notification Message'),
      '#description' => $this->t('Body of the first notification the user receives confirming the enrollment to receive notifications'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getWelcomeMessage(),
      '#group' => 'onesignal_welcome',
    ];

    // Visibility settings.
    $visibility = $this->configManager->getSetting('visibility_pages');
    $pages = $this->configManager->getSetting('pages');
    $form['tracking']['page_display'] = [
      '#type' => 'details',
      '#title' => $this->t('Pages'),
      '#group' => 'tracking_scope',
      '#open' => TRUE,
    ];
  
    if ($visibility == 2) {
      $form['tracking']['page_display'] = [];
      $form['tracking']['page_display']['visibility_pages'] = [
        '#type' => 'value',
        '#value' => 2,
      ];
      $form['tracking']['page_display']['pages'] = [
        '#type' => 'value',
        '#value' => $pages,
      ];
    }
    else {
      $options = [
        $this->t('Every page except the listed pages'),
        $this->t('The listed pages only'),
      ];
      $description_args = [
        '%blog' => 'blog',
        '%blog-wildcard' => 'blog/*',
        '%front' => '<front>',
      ];
      $description = $this->t("Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page.", $description_args);
      $title = $this->t('Pages');
    
      $form['tracking']['page_display']['visibility_pages'] = [
        '#type' => 'radios',
        '#title' => $this->t('Add OneSignal script to specific pages'),
        '#options' => $options,
        '#default_value' => $visibility,
      ];
      $form['tracking']['page_display']['pages'] = [
        '#type' => 'textarea',
        '#title' => $title,
        '#title_display' => 'invisible',
        '#default_value' => $pages,
        '#description' => $description,
        '#rows' => 10,
      ];
    }
  
    // Render the role overview.
    $visibility_roles = $this->configManager->getSetting('roles');
    $form['tracking']['role_display'] = [
      '#type' => 'details',
      '#title' => $this->t('Roles'),
      '#group' => 'tracking_scope',
      '#open' => TRUE,
    ];
  
    $form['tracking']['role_display']['visibility_roles'] = [
      '#type' => 'radios',
      '#title' => $this->t('Add OneSignal script for specific roles'),
      '#options' => [
        $this->t('Add to the selected roles only'),
        $this->t('Add to every role except the selected ones'),
      ],
      '#default_value' => $this->configManager->getSetting('visibility_roles'),
    ];
    $role_options = array_map(['\Drupal\Component\Utility\SafeMarkup', 'checkPlain'], user_role_names());
    $form['tracking']['role_display']['roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Roles'),
      '#default_value' => !empty($visibility_roles) ? $visibility_roles : [],
      '#options' => $role_options,
      '#description' => $this->t('If none of the roles are selected, all users will see the OneSignal subscription popup. If a user has any of the roles checked, that user will see the popup (or excluded, depending on the setting above).'),
    ];
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->configManager->getSetting('enabled'),
      '#description' => 'Uncheck if you want to disable OneSignal throughout the whole site for some reason.',
    ];

    return parent::buildForm($form, $form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $values = $form_state->getValues();

    // Trim some text values.
    $form_state->setValue('pages', trim($values['pages']));
    $form_state->setValue('roles', array_filter($values['roles']));

    // Verify that every path is prefixed with a slash.
    if ($values['visibility_pages'] != 2) {
      $pages = preg_split('/(\r\n?|\n)/', $values['pages']);
      foreach ($pages as $page) {
        if (strpos($page, '/') !== 0 && $page !== '<front>') {
          $form_state->setErrorByName(
            'pages',
            $this->t('Path "@page" not prefixed with slash.', ['@page' => $page])
          );
          // Drupal forms show one error only.
          break;
        }
      }
    }
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    
    $this->config('onesignal.config')
      ->set('onesignal_app_id', $form_state->getValue('onesignal_app_id'))
      ->set('onesignal_safari_web_id', $form_state->getValue('onesignal_safari_web_id'))
      ->set('onesignal_rest_api_key', $form_state->getValue('onesignal_rest_api_key'))
      ->set('onesignal_action_message', $form_state->getValue('onesignal_action_message'))
      ->set('onesignal_accept_button', $form_state->getValue('onesignal_accept_button'))
      ->set('onesignal_cancel_button', $form_state->getValue('onesignal_cancel_button'))
      ->set('onesignal_welcome_title', $form_state->getValue('onesignal_welcome_title'))
      ->set('onesignal_welcome_message', $form_state->getValue('onesignal_welcome_message'))
      ->set('onesignal_auto_register', $form_state->getValue('onesignal_auto_register'))
      ->set('onesignal_notify_button', $form_state->getValue('onesignal_notify_button'))
      ->set('onesignal_localhost_secure', $form_state->getValue('onesignal_localhost_secure'))
      ->set('visibility_pages', $form_state->getValue('visibility_pages'))
      ->set('pages', $form_state->getValue('pages'))
      ->set('visibility_roles', $form_state->getValue('visibility_roles'))
      ->set('roles', $form_state->getValue('roles'))
      ->set('enabled', $form_state->getValue('enabled'))
      ->save();
  }
  
}
