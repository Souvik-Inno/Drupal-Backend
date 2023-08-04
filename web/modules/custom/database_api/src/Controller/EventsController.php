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
    $query->innerJoin('node__field_date', 'nfd', 'n.nid = nfd.entity_id');
    $query->fields('nfd', ['field_date_value']);
    $query->innerJoin('node_field_data', 'nd', 'n.nid = nd.nid');
    $query->fields('nd', ['type', 'title']);
    // $query->groupBy('nfd.field_date_value');
    // $query->addExpression('COUNT(*)', 'count');
    $result = $query->execute()->fetchAll();

    $new_query = $this->connection->select('node__field_date', 'nfd')
                  ->condition('nfd.bundle', 'events', '=')
                  ->fields('nfd', ['field_date_value']);
    $new_result = $new_query->execute()->fetchAll();
    $data = [];
    $titles = [];
    $dates = [];
    $yearly_counts = [];
    foreach ($result as $record) {
      $title = $record->title;
      $type = $record->type;
      $date = $record->field_date_value;
      $as_date = strtotime($date);
      $year = date('Y', $as_date);
      array_push($titles, $title);
      array_push($dates, $date);
      \Drupal::messenger()->addMessage($this->t('type: @type, nid: @nid, title: @title, date: @date, year: @year', 
        ['@type' => $type, '@nid' => $record->nid, '@title' => $title, '@date' => $date, '@year' => $year]));
    }
    foreach ($new_result as $record) {
      $date = strtotime($record->field_date_value);
      $year = date('Y', $date);
      $yearly_counts[$year]++;
    }
    $data = [
      "titles" => $titles,
      "dates" => $dates,
      "yearly_counts" => $yearly_counts,
    ];
    
    return [
      '#theme' => 'database_api',
      '#content' => $data,
    ];
  }

}
