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
 */
class UserForm extends ConfigFormBase {

  /**
   * Constant used to set or get state.
   *
   * @var string
   */
  const FORM_API_CONFIG_PAGE = 'form_api_config_page:values';

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
   * @param \Drupal\Component\Utility\EmailValidatorInterface $email_validator
   *   The email validator.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Object of config factory to get and set values in form.
   */
  public function __construct(EmailValidatorInterface $email_validator, ConfigFactoryInterface $config_factory) {
    $this->emailValidator = $email_validator;
    $this->configFactory = $config_factory;
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
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['form_api.settings'];
  }

  /**
   * {@inheritdoc}
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
      '#suffix' => "<div id='full-name-result' class='red'></div>",
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone Number'),
      '#description' => $this->t('Enter your phone number'),
      '#required' => TRUE,
      '#markup' => "<div id='phone-number-result'></div>",
      '#default_value' => $config->get('phone_number') ?? '',
      '#suffix' => "<div id='phone-number-result' class='red'></div>",
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#description' => $this->t('Enter your email address'),
      '#markup' => "<div id='email-result'></div>",
      '#default_value' => $config->get('email') ?? '',
      '#required' => TRUE,
      '#suffix' => "<div id='email-result' class='red'></div>",
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
      '#attributes' => [
        'id' => 'gender',
      ],
    ];
    $form['other_gender'] = [
      '#type' => 'textfield',
      '#size' => '40',
      '#placeholder' => 'Enter your gender',
      '#states' => [
        'visible' => [
          ':input[id="gender"]' => ['value' => 'other'],
        ],
      ],
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
      '#suffix' => "<div id='submitted'></div>",
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
      $response->addCommand(new HtmlCommand('#submitted', $message));
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
    $config = $this->configFactory()->getEditable('form_api.settings');
    $email_providers = $config->get('email_providers');
    $at_pos = strpos($email_value, '@');
    $dot_pos = strpos($email_value, '.', $at_pos + 1);
    $provider = substr($email_value, $at_pos + 1, $dot_pos - $at_pos - 1);
    if ($at_pos === FALSE || $dot_pos === FALSE) {
      $provider = '';
    }
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
    elseif (!in_array($provider, $email_providers)) {
      $error_array['email-result'] = new HtmlCommand('#email-result', $this->t('Email should be of a valid provider.'));
    }
    elseif (substr($email_value, -4) != ".com") {
      $error_array['email-result'] = new HtmlCommand('#email-result', $this->t('Domain should be .com'));
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
