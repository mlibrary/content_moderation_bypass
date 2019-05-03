<?php

namespace Drupal\content_moderation_bypass;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\content_moderation\Permissions;
use Drupal\workflows\Entity\Workflow;

/**
 * Defines a class for dynamic permissions based on transitions.
 *
 * @internal
 */
class ContentModerationBypassPermission extends Permissions {

  use StringTranslationTrait;

  /**
   * Returns an array of transition permissions.
   *
   * @return array
   *   The transition permissions.
   */
  public function transitionPermissions() {
    $permissions = [];
    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    foreach (Workflow::loadMultipleByType('content_moderation') as $id => $workflow) {
      $permissions['bypass ' . $workflow->id() . ' transition restrictions'] = [
        'title' => $this->t('%workflow workflow: Bypass transition restrictions.', [
          '%workflow' => $workflow->label(),
        ]),
      ];
    }

    return $permissions;
  }

}
