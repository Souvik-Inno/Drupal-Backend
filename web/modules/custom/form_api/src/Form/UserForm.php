<?php

namespace Drupal\form_api\Form;

use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\form_api\UserFormValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides user form for user information.
 *
 * @internal
 */
class UserForm extends ConfigFormBase {

  /**
   * Constant used to set or get state.
   *
   * @var string
   */
  const FORM_API_CONFIG_PAGE = 'form_api_config_page:values';

  /**
   * Form validator.
   *
   * @var \Drupal\form_api\UserFormValidator
   */
  protected $formValidator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_api.user_form_validator')
    );
  }

  /**
   * Contructs the object of the class.
   *
   * @param \Drupal\form_api\UserFormValidator $validator
   *   Object of Validator service class to validate form.
   */
  public function __construct(UserFormValidator $validator) {
    $this->formValidator = $validator;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_api_config_page';
  }

  /**
   * Gets editable config file name.
   */
  public function getEditableConfigNames() {
    return ['form_api.settings'];
  }

  /**
   * Form constructor.
   *
   * Display a tree of all the form elements of user form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state = NULL) {
    $values = \Drupal::state()->get(key: self::FORM_API_CONFIG_PAGE);
    $form = [];
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#description' => $this->t('Enter your full name'),
      '#required' => TRUE,
      '#default_value' => $values['full_name'] ?? '',
    ];
    $form['full-name-result'] = [
      '#type' => 'markup',
      '#markup' => "<div id='full-name-result' class='red'></div>",
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone Number'),
      '#description' => $this->t('Enter your phone number'),
      '#required' => TRUE,
      '#markup' => "<div id='phone-number-result'></div>",
      '#default_value' => $values['phone_number'] ?? '',
    ];
    $form['phone-number-result'] = [
      '#type' => 'markup',
      '#markup' => "<div id='phone-number-result' class='red'></div>",
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#description' => $this->t('Enter your email address'),
      '#markup' => "<div id='email-result'></div>",
      '#required' => TRUE,
    ];
    $form['email-result'] = [
      '#type' => 'markup',
      '#markup' => "<div id='email-result' class='red'></div>",
    ];
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('Gender'),
      '#description' => $this->t('Choose your gender'),
      '#options' => [
        'male' => $this->t('Male'),
        'female' => $this->t('Female'),
        'other' => $this->t('Other'),
      ],
      '#required' => TRUE,
    ];
    $form['actions'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::submitUsingAjax',
        'progress' => [
          'type' => 'throbber',
          'message' => NULL,
        ],
      ],
    ];

    return $form;
  }

  /**
   * Validates the form using AJAX and submits it.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The AJAX response.
   */
  public function submitUsingAjax(array &$form, FormStateInterface $form_state) {
    $result = $this->formValidator->validateForm($form_state);
    $error_count = $this->formValidator->getErrorCount();
    $triggering_element = $form_state->getTriggeringElement();
    if ($error_count == 0  && $triggering_element['#type'] === 'submit') {
      $config = $this->configFactory()->getEditable('form_api.settings');
      $config->set('full_name', $form_state->getValue('full_name'));
      $config->set('phone_number', $form_state->getValue('phone_number'));
      $config->set('email', $form_state->getValue('email'));
      $config->set('gender', $form_state->getValue('gender'));
      $config->save();
      $message = $this->t('Thanks! For Submitting The Form.');
      $result->addCommand(new HtmlCommand('.contact-form-result-message', $message));
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Left Empty as submit is done using AJAX.
  }

}
