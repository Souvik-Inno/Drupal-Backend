<?php

namespace Drupal\database_api\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to get Taxonomy field from user.
 */
class TaxonomyFieldForm extends FormBase {

  /**
   * Connects to the database server.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Sends message to user.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * Contructs an object of the class.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   To set the connection to database.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   To set the messenger.
   */
  public function __construct(Connection $connection, Messenger $messenger) {
    $this->connection = $connection;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'taxonomy_field_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['taxonomy_term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Taxonomy Term Name'),
      '#required' => TRUE,
      '#autocomplete_route_name' => 'database_api.autocomplete_taxonomy',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $taxonomy_term_name = $form_state->getValue('taxonomy_term');
    if (!empty($taxonomy_term_name)) {
      $query = $this->connection->select('taxonomy_term_field_data', 't')
        ->condition('t.name', $taxonomy_term_name, '=')
        ->fields('t', ['name', 'vid']);
      $query_result = $query->execute()->fetchAll();
      if (count($query_result) == 0) {
        $form_state->setErrorByName('taxonomy_term', 'Wrong Taxonomy term given.');
      }
    }
    else {
      $form_state->setErrorByName('taxonomy_term', 'No Taxonomy term given.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $taxonomy_term_name = $form_state->getValue('taxonomy_term');
    $query = $this->connection->select('taxonomy_term_field_data', 't')
      ->condition('t.name', $taxonomy_term_name, '=')
      ->fields('t', ['name', 'tid']);
    $query->innerJoin('taxonomy_term_data', 'td', 't.tid = td.tid');
    $query->fields('td', ['tid', 'uuid']);
    $query->innerJoin('taxonomy_index', 'ti', 'ti.tid = t.tid');
    $query->fields('ti', ['tid', 'nid']);
    $query->leftJoin('node_field_data', 'nfd', 'nfd.nid = ti.nid');
    $query->fields('nfd', ['title', 'nid']);
    $result = $query->execute()->fetchAll();
    $nodedata = [];
    foreach ($result as $row) {
      $this->messenger->addStatus($this->t("tid: @tid, uuid: @uuid",
        ['@tid' => $row->tid, '@uuid' => $row->uuid]));
      $nodedata[] = [
        "title" => $row->title,
        "nid" => $row->nid,
      ];
    }
    foreach ($nodedata as $data) {
      $this->messenger->addStatus($this->t("Title of node: @title", ['@title' => $data['title']]));
      $url = Url::fromRoute('entity.node.canonical', ['node' => $data['nid']]);
      $this->messenger->addStatus($this->t('You can <a href=":url">Click Here</a> to view the node.', [':url' => $url->toString()]));
    }
  }

}
