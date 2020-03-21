<?php

namespace Drupal\onesignal;

use Drupal\Core\Entity\EntityInterface;

/**
 * Provides and interface for OnesignalGenerator.
 */
interface OnesignalGeneratorInterface {
  
  /**
   * Apply patterns to create an notification.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $op
   *   Operation being performed on the content being aliased
   *   ('insert', 'update').
   *
   * @return array|string
   *   The notification that was created.
   */
  public function createNotification(EntityInterface $entity, $op);
  
  /**
   * Load an OneSignal pattern entity by entity, bundle, and language.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   *
   * @return \Drupal\onesignal\Entity\OnesignalPatternInterface|null
   */
  public function getPatternByEntity(EntityInterface $entity);
  
}
