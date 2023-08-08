<?php

namespace Drupal\database_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
   * Contructs an object of the class.
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
    $query = $this->connection->select('node__field_date', 'nfd')
      ->condition('nfd.bundle', 'events', '=')
      ->fields('nfd', ['field_date_value', 'entity_id']);
    $query->innerJoin('node__field_type', 'nft', 'nfd.entity_id = nft.entity_id');
    $query->fields('nft', ['field_type_value', 'entity_id']);
    $result = $query->execute()->fetchAll();
    $yearly_counts = [];
    $quarterly_counts = [];
    $type_counts = [];
    foreach ($result as $record) {
      $date = strtotime($record->field_date_value);
      $year = date('Y', $date);
      $quarter = ceil(date('n', $date) / 3);
      if (array_key_exists($year, $yearly_counts)) {
        $yearly_counts[$year]++;
      }
      else {
        $yearly_counts[$year] = 1;
      }
      switch ($quarter) {
        case 1:
          $quarter_key = "Jan to Mar " . $year;
          break;

        case 2:
          $quarter_key = "Apr to June " . $year;
          break;

        case 3:
          $quarter_key = "July to Sep " . $year;
          break;

        case 4:
          $quarter_key = "Oct to Dec " . $year;
          break;

        default:
          $quarter_key = "Invalid";
      }
      if (array_key_exists($quarter_key, $quarterly_counts)) {
        $quarterly_counts[$quarter_key]++;
      }
      else {
        $quarterly_counts[$quarter_key] = 1;
      }
      if (array_key_exists($record->field_type_value, $type_counts)) {
        $type_counts[$record->field_type_value]++;
      }
      else {
        $type_counts[$record->field_type_value] = 1;
      }
    }
    $data = [
      "yearly_counts" => $yearly_counts,
      "quarterly_counts" => $quarterly_counts,
      "type_counts" => $type_counts,
    ];
    return [
      '#theme' => 'database_api',
      '#content' => $data,
    ];
  }

  /**
   * Autocompletes the taxonomy ternm name for form.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object to get the taxonomy term name from form.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response object.
   */
  public function autocompleteTaxonomy(Request $request) {
    $term_name = $request->query->get('q');
    $results = [];
    if (!empty($term_name)) {
      $query = $this->connection->select('taxonomy_term_field_data', 't')
        ->condition('t.name', '%' . $term_name . '%', 'LIKE')
        ->fields('t', ['name', 'vid'])
        ->range(0, 10);
      $query_result = $query->execute()->fetchAll();
      if (!empty($query_result)) {
        foreach ($query_result as $term) {
          $results[] = $term->name;
        }
      }
    }
    return new JsonResponse($results);
  }

}
