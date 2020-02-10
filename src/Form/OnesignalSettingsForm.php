<?php

namespace Drupal\onesignal\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
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
   * {@inheritdoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager) {
    
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
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
    
    $form['enabled_entity_types'] = [
      '#type' => 'details',
      '#open' => TRUE,
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
    
    return parent::buildForm($form, $form_state);
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
