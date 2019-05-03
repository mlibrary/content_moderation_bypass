<?php

namespace Drupal\content_moderation_bypass\Plugin\WorkflowType;

use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\content_moderation_bypass\ContentModerationBypassState;
use Drupal\workflows\Plugin\WorkflowTypeBase;
use Drupal\workflows\StateInterface;
use Drupal\workflows\WorkflowInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\content_moderation\Plugin\WorkflowType\ContentModeration;
use Drupal\content_moderation\Plugin\WorkflowType\ContentModerationInterface;
use Drupal\workflows\TransitionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\content_moderation_bypass\ModerationBypassInformationInterface;
use Drupal\workflows\Entity\Workflow;

/**
 * Attaches workflows to content entity types and their bundles.
 *
 * @WorkflowType(
 *   id = "content_moderation",
 *   label = @Translation("Content moderation"),
 *   required_states = {
 *     "draft",
 *     "published",
 *   },
 *   forms = {
 *     "configure" = "\Drupal\content_moderation\Form\ContentModerationConfigureForm",
 *     "state" = "\Drupal\content_moderation\Form\ContentModerationStateForm"
 *   },
 * )
 */
class ContentModerationBypass extends ContentModeration implements ContentModerationInterface, ContainerFactoryPluginInterface {

  /**
   * Current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Extend the plugin without altering constructor.
   * See https://www.previousnext.com.au/blog/safely-extending-drupal-8-plugin-classes-without-fear-of-constructor-changes
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $static = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $static->setAccountInterface($container->get('current_user'));
    return $static;
  }

  /**
  * Sets AccountInterface.
  */
  public function setAccountInterface(AccountInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public function getTransitionsForState($state_id, $direction = TransitionInterface::DIRECTION_FROM) {
    foreach (Workflow::loadMultipleByType('content_moderation') as $workflow) {
      if (in_array($this->getState($state_id), $workflow->getTypePlugin()->getStates())) {
        if ($this->currentUser->hasPermission('bypass ' . $workflow->id() . ' transition restrictions')) {
          return $this->getTransitions(array_keys($this->configuration['transitions']));
        }
      }
    }
    
    // Code from base class.
    $transition_ids = array_keys(array_filter($this->configuration['transitions'], function ($transition) use ($state_id, $direction) {
      return in_array($state_id, (array) $transition[$direction], TRUE);
    }));
    return $this->getTransitions($transition_ids);
  }

}
