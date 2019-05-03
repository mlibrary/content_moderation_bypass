<?php

namespace Drupal\content_moderation_bypass\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Drupal\content_moderation\Plugin\Validation\Constraint\ModerationStateConstraint;

/**
 * Verifies that nodes have a valid moderation state.
 *
 * @Constraint(
 *   id = "BypassModerationState",
 *   label = @Translation("Valid moderation state", context = "Validation")
 * )
 */
class BypassModerationStateConstraint extends ModerationStateConstraint {

  public $message = 'Invalid state transition from %from to %to';
  public $invalidStateMessage = 'State %state does not exist on %workflow workflow';
  public $invalidTransitionAccess = 'You do not have access to transition from %original_state to %new_state';

}
