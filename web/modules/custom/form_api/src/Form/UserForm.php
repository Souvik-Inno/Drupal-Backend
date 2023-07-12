<?php

namespace Drupal\form_api\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Ajax\AddCssCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
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
   * Validates the email.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;
  /**
   * Gets value from config.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Contructs the object of the class.
   *
   * @param \Drupal\Component\Utility\EmailValidatorInterface $emailValidator
   *   The email validator.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Object of config factory to get and set values in form.
   */
  public function __construct(EmailValidatorInterface $emailValidator, ConfigFactoryInterface $configFactory) {
    $this->emailValidator = $emailValidator;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('email.validator'),
      $container->get('config.factory')
    );
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
    $config = $this->configFactory->get('form_api.settings');
    $form = [];
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#description' => $this->t('Enter your full name'),
      '#required' => TRUE,
      '#default_value' => $config->get('full_name') ?? '',
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
      '#default_value' => $config->get('phone_number') ?? '',
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
      '#default_value' => $config->get('email') ?? '',
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
      '#default_value' => $config->get('gender'),
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
    $error_array = $this->validateFormUsingAjax($form_state);
    $triggering_element = $form_state->getTriggeringElement();
    $response = new AjaxResponse();
    if (count($error_array) == 0  && $triggering_element['#type'] === 'submit') {
      $config = $this->configFactory()->getEditable('form_api.settings');
      $config->set('full_name', $form_state->getValue('full_name'));
      $config->set('phone_number', $form_state->getValue('phone_number'));
      $config->set('email', $form_state->getValue('email'));
      $config->set('gender', $form_state->getValue('gender'));
      $config->save();
      $message = $this->t('Thanks! For Submitting The Form.');
      $response->addCommand(new HtmlCommand('.contact-form-result-message', $message));
    }
    else {
      $css_string = '<style>.red{color:red;}</style>';
      if (array_key_exists('full-name-result', $error_array)) {
        $response->addCommand($error_array['full-name-result']);
      }
      if (array_key_exists('phone-number-result', $error_array)) {
        $response->addCommand($error_array['phone-number-result']);
      }
      if (array_key_exists('email-result', $error_array)) {
        $response->addCommand($error_array['email-result']);
      }
      $response->addCommand(new AddCssCommand($css_string));
    }
    return $response;
  }

  /**
   * Validates the form and returns AJAX response.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\HtmlCommand[]
   *   The array containing all error messages.
   */
  public function validateFormUsingAjax(FormStateInterface $form_state) {
    $phone_number = $form_state->getValue('phone_number');
    $email_value = $form_state->getValue('email');
    $full_name = $form_state->getValue('full_name');
    $email_validator = $this->emailValidator->isValid($email_value);
    $email_providers = [
      'gmail.com',
      'yahoo.com',
      'outlook.com',
      'hotmail.com',
      'mail.com',
      'zoho.com',
    ];
    $domain = strtolower(substr(strrchr($email_value, "@"), 1));
    $error_array = [];
    if (!preg_match("/^[A-Z a-z]+$/", $full_name)) {
      $error_array['full-name-result'] = new HtmlCommand('#full-name-result', $this->t('Name should be text only.'));
    }
    if (!preg_match('/^\+91\d{10}$/', $phone_number)) {
      $error_array['phone-number-result'] = new HtmlCommand('#phone-number-result', $this->t('Only Indian phone numbers are allowed with 10 digits.'));
    }
    if (!$email_validator) {
      $error_array['email-result'] = new HtmlCommand('#email-result', $this->t('Enter valid Email'));
    }
    elseif (!in_array($domain, $email_providers)) {
      $error_array['email-result'] = new HtmlCommand('#email-result', $this->t('Enter valid Email Domain'));
    }
    return $error_array;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Left Empty as submit is done using AJAX.
  }

}
