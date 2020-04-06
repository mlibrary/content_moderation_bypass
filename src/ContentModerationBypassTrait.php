<?php

namespace Drupal\content_moderation_bypass;

use Drupal\workflows\WorkflowInterface;

/**
 * Provides a trait for the bypass permission.
 */
trait ContentModerationBypassTrait {

  /**
   * Returns the workflow permission string.
   *
   * @return string
   *   The permission string.
   */
  public static function permissionForWorkflow(WorkflowInterface $workflow) {
    return t('bypass @workflow_id transition restrictions', ['@workflow_id' => $workflow->id()]);
  }

}
