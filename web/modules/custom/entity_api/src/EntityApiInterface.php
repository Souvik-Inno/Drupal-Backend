<?php

declare(strict_types = 1);

namespace Drupal\entity_api;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an entity api entity type.
 */
interface EntityApiInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
