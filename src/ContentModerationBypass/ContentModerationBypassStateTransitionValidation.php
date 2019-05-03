<?php

namespace Drupal\content_moderation_bypass\ContentModerationBypass;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\workflows\StateInterface;
use Drupal\workflows\Transition;
use Drupal\workflows\WorkflowInterface;
use Drupal\content_moderation\StateTransitionValidation;
use Drupal\content_moderation\StateTransitionValidationInterface;
use Drupal\content_moderation\ModerationInformationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Validates whether a certain state transition is allowed.
 */
class ContentModerationBypassStateTransitionValidation extends StateTransitionValidation implements StateTransitionValidationInterface {

  /**
   * {@inheritdoc}
   */
  public function getValidTransitions(ContentEntityInterface $entity, AccountInterface $user) {
    $workflow = $this->moderationInfo->getWorkflowForEntity($entity);
    $current_state = $entity->moderation_state->value ? $workflow->getTypePlugin()->getState($entity->moderation_state->value) : $workflow->getTypePlugin()->getInitialState($entity);

    if ($user->hasPermission('bypass ' . $workflow->id() . ' transition restrictions')) {
      return array_filter($current_state->getTransitions(), function (Transition $transition) use ($workflow, $user) {
        return $user->hasPermission('bypass ' . $workflow->id() . ' transition restrictions');
      });
    }

    return array_filter($current_state->getTransitions(), function (Transition $transition) use ($workflow, $user) {
      return $user->hasPermission('use ' . $workflow->id() . ' transition ' . $transition->id());
    });
  }

  /**
   * {@inheritdoc}
   */
  public function isTransitionValid(WorkflowInterface $workflow, StateInterface $original_state, StateInterface $new_state, AccountInterface $user, ContentEntityInterface $entity = NULL) {
    if ($user->hasPermission('bypass ' . $workflow->id() . ' transition restrictions')) {
      return $user->hasPermission('bypass ' . $workflow->id() . ' transition restrictions');
    }
    if ($entity === NULL) {
      @trigger_error(sprintf('Omitting the $entity parameter from %s is deprecated and will be required in Drupal 9.0.0.', __METHOD__), E_USER_DEPRECATED);
    }
    $transition = $workflow->getTypePlugin()->getTransitionFromStateToState($original_state->id(), $new_state->id());
    return $user->hasPermission('use ' . $workflow->id() . ' transition ' . $transition->id());
  }

}
