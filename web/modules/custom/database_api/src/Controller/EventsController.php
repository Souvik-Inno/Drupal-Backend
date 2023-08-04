<?php

namespace Drupal\database_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for database_api routes.
 */
class EventsController extends ControllerBase {

  /**
   * Connects to the database server.
   * 
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Contructs an object of the class
   * 
   * @param \Drupal\Core\Database\Connection $connection
   *   To set the connection to database.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }
  
  /**
   * Renders the dashboard to display listing of counts of events.
   * 
   * @return array
   *   Renderable array to be displayed.
   */
  public function listing() {
    $query = $this->connection->select('node', 'n');
    $query->condition('n.type', 'events', '=')
          ->fields('n', ['nid', 'type']);
    $query->innerJoin('node_field_date', 'nfd', 'n.nid = nfd.entity_id')
          ->fields('nfd', ['field_date_value']);
    $result = $query->execute();
    $titles = [];
    $dates = [];
    foreach ($result as $record) {
      $node = Node::load($record->nid);
      $title = $node->getTitle();
      $type = $node->get('field_type')->getString();
      $date = $node->get('field_date')->getString();
      array_push($titles, $title);
      array_push($dates, $date);
      \Drupal::messenger()->addMessage($this->t('type: @type, nid: @nid, title: @title, date: @date', 
        ['@type' => $type, '@nid' => $record->nid, '@title' => $title, '@date' => $date]));
    }
    
    return [
      '#theme' => 'database_api',
      '#content' => $titles,
    ];
  }

}
