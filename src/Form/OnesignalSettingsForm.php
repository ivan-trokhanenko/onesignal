<?php

namespace Drupal\onesignal\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\onesignal\Config\ConfigManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Configure available entity types for Onesignal.
 */
class OnesignalSettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The config manager service.
   *
   * @var \Drupal\onesignal\Config\ConfigManagerInterface
   */
  private $configManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    ConfigManagerInterface $configManager) {
    
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->configManager = $configManager;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('onesignal.config_manager')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'onesignal_settings_form';
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['onesignal.settings'];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('onesignal.settings');
  
    $form['onesignal_api'] = [
      '#type' => 'details',
      '#title' => $this->t('API settings'),
      '#description' => $this->t('Manage OneSignal API keys.'),
    ];
    $form['onesignal_api']['onesignal_app_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OneSignal App ID'),
      '#description' => $this->t('Find it at https://OneSignal.com under your app Settings &gt; Keys &amp; IDs.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getAppId(),
      '#group' => 'onesignal_api',
    ];
    // Disable the field if its value has been set in the settings.php file.
    // This is useful when there are different configurations for development,
    // testing and live environments.
    if (!empty($this->configManager->getAppId()) && ($this->configManager->getOriginalAppId() != $this->configManager->getAppId())) {
      $form['onesignal_app_id']['#disabled'] = TRUE;
      $form['onesignal_app_id']['#description'] = $this->t('This field has been disabled because its value is being overridden in the settings.php file.');
    }
    $form['onesignal_api']['onesignal_safari_web_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OneSignal Safari App ID'),
      '#description' => $this->t('Find it at https://OneSignal.com under your app Settings &gt; Platforms &gt; Apple Safari.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getSafariWebId(),
      '#group' => 'onesignal_api',
    ];
    // Disable the field if its value has been set in the settings.php file
    // This is useful when there are different configurations for development,
    // testing and live environments.
    if (!empty($this->configManager->getSafariWebId()) && ($this->configManager->getOriginalSafariWebId() != $this->configManager->getSafariWebId())) {
      $form['onesignal_safari_web_id']['#disabled'] = TRUE;
      $form['onesignal_safari_web_id']['#description'] = $this->t('This field has been disabled because its value is being overridden in the settings.php file.');
    }
    $form['onesignal_api']['onesignal_rest_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OneSignal REST API key'),
      '#description' => $this->t('REST API key.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configManager->getRestApiKey(),
      '#group' => 'onesignal_api',
    ];
    $form['onesignal_api']['onesignal_auto_register'] = [
      '#type' => 'select',
      '#title' => $this->t('Auto register'),
      '#description' => $this->t('Set to true to automatically prompt visitors to accept notifications.'),
      '#options' => [
        0 => $this->t('Unset'),
        'true' => $this->t('True'),
        'false' => $this->t('False'),
      ],
      '#default_value' => $this->configManager->getAutoRegister(),
      // Uncomment once https://www.drupal.org/project/drupal/issues/2854166 resolved.
      // '#group' => 'onesignal_api',
    ];
    $form['onesignal_api']['onesignal_notify_button'] = [
      '#type' => 'select',
      '#title' => $this->t('Notify button visibility'),
      '#description' => $this->t('True will make the Bell visible if it has been configured at OneSignal.'),
      '#options' => [
        0 => $this->t('Unset'),
        'true' => $this->t('True'),
        'false' => $this->t('False'),
      ],
      '#default_value' => $this->configManager->getNotifyButton(),
      // Uncomment once https://www.drupal.org/project/drupal/issues/2854166 resolved.
      // '#group' => 'onesignal_api',
    ];
    $form['onesignal_api']['onesignal_localhost_secure'] = [
      '#type' => 'select',
      '#title' => $this->t('Localhost secure origin'),
      '#description' => $this->t('Development setting. True allows a Locahost behave as if it had SSL.'),
      '#options' => [
        0 => $this->t('Unset'),
        'true' => $this->t('True'),
        'false' => $this->t('False'),
      ],
      '#default_value' => $this->configManager->getLocalhostSecure(),
      // Uncomment once https://www.drupal.org/project/drupal/issues/2854166 resolved.
      // '#group' => 'onesignal_api',
    ];

    // Prompt settings.
    $form['onesignal_prompt'] = [
      '#type' => 'details',
      '#title' => $this->t('Prompt settings'),
      '#description' => $this->t('Controls the box that prompts the user to receive notifications. Leave blank to use what comes from OneSignal.'),
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
    
    // Welcome settings.
    $form['onesignal_welcome'] = [
      '#type' => 'details',
      '#title' => $this->t('Welcome settings'),
      '#description' => $this->t('Controls the first message sent confirming the sign up. Leave blank to use what comes from OneSignal.'),
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
    
    $form['enabled_entity_types'] = [
      '#type' => 'details',
      '#title' => $this->t('Enabled entity types'),
      '#description' => $this->t('Enable to add a notification and allow to define OneSignal patterns for the given type. Disabled types already define a path field themselves or currently have a OneSignal pattern.'),
      '#tree' => TRUE,
    ];
    
    // Get all applicable entity types.
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type) {
      // Disable a checkbox if it already exists and if the entity type has
      // patterns currently defined or if it isn't defined by us.
      $patterns_count = \Drupal::entityQuery('onesignal_pattern')
        ->condition('type', 'canonical_entities:' . $entity_type_id)
        ->count()
        ->execute();
      
      if (is_subclass_of($entity_type->getClass(), FieldableEntityInterface::class) && $entity_type->hasLinkTemplate('canonical')) {
        $field_definitions = $this->entityFieldManager->getBaseFieldDefinitions($entity_type_id);
        $form['enabled_entity_types'][$entity_type_id] = [
          '#type' => 'checkbox',
          '#title' => $entity_type->getLabel(),
          '#default_value' => isset($field_definitions['path']) || in_array($entity_type_id, $config->get('enabled_entity_types')),
//          '#disabled' => isset($field_definitions['path']) && ($field_definitions['path']->getProvider() != 'pathauto' || $patterns_count),
        ];
      }
    }

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->configManager->getSetting('enabled'),
      '#description' => $this->t('Uncheck if you want to disable OneSignal throughout the whole site for some reason.'),
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
    $config = $this->config('onesignal.settings');
    
    $form_state->cleanValues();
    
    foreach ($form_state->getValues() as $key => $value) {
      if ($key == 'enabled_entity_types') {
        $enabled_entity_types = [];
        foreach ($value as $entity_type_id => $enabled) {
          $field_definitions = $this->entityFieldManager->getBaseFieldDefinitions($entity_type_id);
          // Verify that the entity type is enabled and that it is not defined
          // or defined by us before adding it to the configuration, so that
          // we do not store an entity type that cannot be enabled or disabled.
          if ($enabled && (!isset($field_definitions['path']) || ($field_definitions['path']->getProvider() === 'pathauto'))) {
            $enabled_entity_types[] = $entity_type_id;
          }
        }
        $value = $enabled_entity_types;
      }
      $config->set($key, $value);
    }
    $config->save();
    
    parent::submitForm($form, $form_state);
  }

}
