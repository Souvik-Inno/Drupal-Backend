<?php

namespace Drupal\form_api;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Ajax\AddCssCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a service to validate user's form.
 */
class UserFormValidator {

  /**
   * Counts the number of errors in the form.
   *
   * @var int
   */
  protected $errorCount = 0;

  /**
   * Validates the email.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * Constructs object of the class.
   *
   * @param \Drupal\Component\Utility\EmailValidatorInterface $emailValidator
   *   The email validator.
   */
  public function __construct(EmailValidatorInterface $emailValidator) {
    $this->emailValidator = $emailValidator;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('email.validator')
    );
  }

  /**
   * Validates the form and returns AJAX response.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The AJAX response.
   */
  public function validateForm(FormStateInterface $form_state) {
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
    $response = new AjaxResponse();
    $css_string = '<style>.red{color:red;}</style>';
    if (!preg_match("/^[A-Za-z]+$/", $full_name)) {
      $response->addCommand(new HtmlCommand('#full-name-result', t('Name should be text only.')));
      $this->errorCount++;
    }
    if (!preg_match('/^\+91\d{10}$/', $phone_number)) {
      $response->addCommand(new HtmlCommand('#phone-number-result', t('Only Indian phone numbers are allowed with 10 digits.')));
      $this->errorCount++;
    }
    if (!$email_validator) {
      $response->addCommand(new HtmlCommand('#email-result', t('Enter valid Email')));
      $this->errorCount++;
    }
    elseif (!in_array($domain, $email_providers)) {
      $response->addCommand(new HtmlCommand('#email-result', t('Enter valid Email Domain')));
      $this->errorCount++;
    }
    $response->addCommand(new AddCssCommand($css_string));
    return $response;
  }

  /**
   * Returns the error count for the form.
   *
   * @return int
   *   The error count.
   */
  public function getErrorCount() {
    return $this->errorCount;
  }

}
