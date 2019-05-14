<?php

namespace Drupal\content_moderation_bypass\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\Validator\Constraint;
use Drupal\content_moderation\Plugin\Validation\Constraint\ModerationStateConstraintValidator;
use Drupal\content_moderation_bypass\ContentModerationBypassTrait;

/**
 * Checks if a moderation state transition is valid.
 */
class BypassModerationStateConstraintValidator extends ModerationStateConstraintValidator implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $value->getEntity();


    // Ignore entities that are not subject to moderation anyway.
    if (!$this->moderationInformation->isModeratedEntity($entity)) {
      return;
    }

    $workflow = $this->moderationInformation->getWorkflowForEntity($entity);
    if ($this->currentUser->hasPermission(ContentModerationBypassTrait::permissionForWorkflow($workflow))) {
      // Remove standard moderation state violations.
      foreach ($this->context->getViolations() as $key => $violation) {
        if ($violation->getPropertyPath() == 'moderation_state') {
          $this->context->getViolations()->remove($key);
        }
      }
    }
  }

}
