<?php

namespace Drupal\menu_api\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to save budget for movie.
 */
class MenuApiConfigForm extends ConfigFormBase {

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
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'menu_api_config_page';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['menu_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state = NULL) {
    $config = $this->configFactory->get('menu_api.settings');
    $form = parent::buildForm($form, $form_state);
    $form['budget'] = [
      '#type' => 'number',
      '#title' => $this->t('Enter your budget'),
      '#default_value' => $config->get('movie_budget'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('menu_api.settings');
    $config->set('movie_budget', $form_state->getValue('budget'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
