<?php

namespace Drupal\onesignal;

use Drupal\onesignal\OnesignalNotificationManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Utility\Token;
use Drupal\token\TokenEntityMapperInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides methods for generating notification.
 */
class OnesignalGenerator implements OnesignalGeneratorInterface {
  
  /**
   * The OneSignal notification manager.
   *
   * @var \Drupal\onesignal\OnesignalNotificationManagerInterface
   */
  protected $notificationManager;
  
  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;
  
  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;
  
  /**
   * The token entity mapper.
   *
   * @var \Drupal\token\TokenEntityMapperInterface
   */
  protected $tokenEntityMapper;
  
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  
  /**
   * OnesignalGenerator constructor.
   *
   * @param \Drupal\onesignal\OnesignalNotificationManagerInterface $notificationManager
   *   The OneSignal notification manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\token\TokenEntityMapperInterface $tokenEntityMapper
   *   The token entity mapper.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    OnesignalNotificationManagerInterface $notificationManager,
    MessengerInterface $messenger,
    Token $token,
    TokenEntityMapperInterface $tokenEntityMapper,
    EntityTypeManagerInterface $entityTypeManager) {
    
    $this->notificationManager = $notificationManager;
    $this->messenger = $messenger;
    $this->token = $token;
    $this->tokenEntityMapper = $tokenEntityMapper;
    $this->entityTypeManager = $entityTypeManager;
  }
  
  /**
   * {@inheritdoc}
   */
  public function createNotification(EntityInterface $entity, $op) {
    // Retrieve and apply the pattern for this entity type.
    $pattern = $this->getPatternByEntity($entity);
    if (empty($pattern)) {
      // No pattern? Do nothing.
      return NULL;
    }
    // TODO: Implement createNotification() method.
    //$this->notificationManager->allSubscribersSend();
  
  }
  
  /**
   * {@inheritdoc}
   */
  public function getPatternByEntity(EntityInterface $entity) {
  
  }
  
}
