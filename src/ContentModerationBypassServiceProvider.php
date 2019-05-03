<?php

namespace Drupal\content_moderation_bypass;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Service Provider for Entity Reference Revisions.
 */
class ContentModerationBypassServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('content_moderation.state_transition_validation');
    $definition->setClass('\Drupal\content_moderation_bypass\ContentModerationBypass\ContentModerationBypassStateTransitionValidation');
  }

}
