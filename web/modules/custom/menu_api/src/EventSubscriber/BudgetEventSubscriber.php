<?php

namespace Drupal\menu_api\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
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
   * Contructs the object of the class.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Object of config factory to get and set values in form.
   * @param \Drupal\Core\Routing\RouteMatchInterface $current_route_match
   *   Object of Route Match Interface to get the current route match.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RouteMatchInterface $current_route_match) {
    $this->configFactory = $config_factory;
    $this->routeMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::VIEW][] = ['onResponse', 10];
    return $events;
  }

  /**
   * Converts a BudgetEventSubscriber response to a Symfony response.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   The Event to process.
   */
  public function onResponse(ViewEvent $event) {
    $route_name = $this->routeMatch->getRouteName();
    if ($route_name === 'entity.node.canonical') {
      $node = $this->routeMatch->getParameter('node');
      if ($node instanceof \Drupal\node\NodeInterface) {
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
          $node->body->value = $output . $node->body->value;
        }
      }
    }
  }
}
