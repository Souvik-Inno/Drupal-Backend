<?php

namespace Drupal\form_api;

use Drupal\Core\Ajax\AddCssCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a service to validate user's form.
 */
class UserFormValidator {

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
    $email_domain = substr($email_value, -4);
    $email_validator = \Drupal::service('email.validator')->isValid($email_value);
    $response = new AjaxResponse();
    $css_string = '<style>.red{color:red;}</style>'; 
    if (!preg_match("/^[A-Za-z]+$/", $full_name)) {
      $response->addCommand(new HtmlCommand('#full-name-result', 'Name should be text only.'));
    }
    if (!preg_match('/^\+91\d{10}$/', $phone_number)) {
      $response->addCommand(new HtmlCommand('#phone-number-result', 'Only Indian phone numbers are allowed with 10 digits.'));
    }
    if (!$email_validator) {
      $response->addCommand(new HtmlCommand('#email-result', 'Enter valid Email'));
    }
    elseif ($email_domain != '.com') {
      $response->addCommand(new HtmlCommand('#email-result', 'Email should be of .com domain'));
    }
    $response->addCommand(new AddCssCommand($css_string));
    return $response;
  }

}
