<?php

namespace Drupal\form_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\form_api\UserFormValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides user form for user information.
 *
 * @internal
 */
class UserForm extends FormBase {

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
  public function buildForm(array $form, FormStateInterface $formState = NULL) {
    $values = \Drupal::state()->get(key: self::FORM_API_CONFIG_PAGE);
    $form = [];
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t(string: 'Full Name'),
      '#description' => $this->t(string: 'Enter your full name'),
      '#required' => TRUE,
      '#default_value' => $values['full_name'] ?? '',
    ];
    $form['full-name-result'] = [
      '#type' => 'markup',
      '#markup' => "<div id='full-name-result' class='red'></div>",
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t(string: 'Phone Number'),
      '#description' => $this->t(string: 'Enter your phone number'),
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
      '#title' => $this->t(string: 'Email Address'),
      '#description' => $this->t(string: 'Enter your email address'),
      '#markup' => "<div id='email-result'></div>",
      '#required' => TRUE,
    ];
    $form['email-result'] = [
      '#type' => 'markup',
      '#markup' => "<div id='email-result' class='red'></div>",
    ];
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t(string: 'Gender'),
      '#description' => $this->t(string: 'Choose your gender'),
      '#options' => [
        'male' => $this->t(string: 'Male'),
        'female' => $this->t(string: 'Female'),
        'other' => $this->t(string: 'Other'),
      ],
      '#required' => TRUE,
    ];
    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::validateUsingAjax',
        'progress' => [
          'type' => 'throbber',
          'message' => NULL,
        ],
      ],
    ];

    return $form;
  }

  /**
   * Validates the form using AJAX.
   * 
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * 
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The AJAX response.
   */
  public function validateUsingAjax(array &$form, FormStateInterface $form_state) {
    return $this->formValidator->validateForm($form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submitted_values = $form_state->cleanValues()->getValues();
    \Drupal::state()->set(self::FORM_API_CONFIG_PAGE, $submitted_values);
    \Drupal::service(id: 'messenger')->addStatus($this->t(string: 'Your form has been submitted successfully'));
  }

}
