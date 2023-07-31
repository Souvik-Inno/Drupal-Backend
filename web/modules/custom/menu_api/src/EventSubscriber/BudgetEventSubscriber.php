<?php

namespace Drupal\menu_api\EventSubscriber;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscribe event for movie budget analysis service.
 */
class BudgetEventSubscriber implements EventSubscriberInterface {

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Gets value from config.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Invalidates required tags.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $invalidator;

  /**
   * Contructs the object of the class.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Object of config factory to get and set values in form.
   * @param \Drupal\Core\Routing\RouteMatchInterface $current_route_match
   *   Object of Route Match Interface to get the current route match.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $invalidator
   *   Object of Cache Tags Invalidator to invalidate required tags.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RouteMatchInterface $current_route_match, CacheTagsInvalidatorInterface $invalidator) {
    $this->configFactory = $config_factory;
    $this->routeMatch = $current_route_match;
    $this->invalidator = $invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::VIEW][] = ['onResponse', 100];
    $events[ConfigEvents::SAVE][] = ['onConfigSave', 110];
    return $events;
  }

  /**
   * Converts a ViewEvent response to a Symfony response.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   The Event to process.
   */
  public function onResponse(ViewEvent $event) {
    $route_name = $this->routeMatch->getRouteName();
    if ($route_name === 'entity.node.canonical') {
      $node = $this->routeMatch->getParameter('node');
      if ($node instanceof NodeInterface) {
        $bundle = $node->getType();
        $config = $this->configFactory->get('menu_api.settings');
        if ($bundle === 'movie') {
          $budget = $config->get('movie_budget');
          $price = $node->get('field_movie_price')->value;
          if ($budget < $price) {
            $output = '<h3>The movie is over Budget</h3>';
          }
          elseif ($budget > $price) {
            $output = '<h3>The movie is under Budget</h3>';
          }
          else {
            $output = '<h3>The movie is within Budget</h3>';
          }
          $build['movie_price'] = [
            '#type' => 'markup',
            '#markup' => $output,
            '#cache' => [
              'tags' => ['movie_budget'],
            ],
          ];
          $controller_result = $event->getControllerResult();
          $event->setControllerResult(array_merge($build, $controller_result));
        }
      }
    }
  }

  /**
   * Invalidates tag when config is saved.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The event to get the config data.
   */
  public function onConfigSave(ConfigCrudEvent $event) {
    $config_name = $event->getConfig()->getName();
    if ($config_name === 'menu_api.settings' && $event->isChanged('movie_budget')) {
      $this->invalidator->invalidateTags(['movie_budget']);
    }
  }

}
