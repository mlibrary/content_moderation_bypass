<?php

namespace Drupal\content_moderation_bypass\Plugin\Validation\Constraint;

use Drupal\content_moderation\StateTransitionValidationInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Validation\Plugin\Validation\Constraint\NotNullConstraint;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
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
    $workflow = $this->moderationInformation->getWorkflowForEntity($entity);
    if ($this->currentUser->hasPermission(ContentModerationBypassTrait::permissionForWorkflow($workflow))) {
      // remove standard moderation state violations.
      foreach ($this->context->getViolations() as $key => $violation) {
        if ($violation->getPropertyPath() == 'moderation_state') {
          $this->context->getViolations()->remove($key);
        }
      }
    }
  }

}
