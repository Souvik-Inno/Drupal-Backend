<?php

declare(strict_types = 1);

namespace Drupal\block_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Returns responses for Block API routes.
 */
final class BlockApiController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $user_tag = ['user:' . $this->currentUser()->id()];
    $current_user = $this->currentUser()->getAccountName();
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works! Congrats @current_user', ['@current_user' => $current_user]),
      '#cache' => [
        'tags' => $user_tag,
      ],
    ];

    return $build;
  }

  /**
   * Displays data on custom page.
   *
   * @return array
   *   Render-able array to display on custom page.
   */
  public function customWelcome() {
    return [
      '#markup' => $this->t('You have a granted access to the page.'),
    ];
  }

  /**
   * Displays data from route with user id and status.
   *
   * @param int $id
   *   The user id.
   * @param int $status
   *   The status of log in.
   *
   * @return array
   *   Render-able array to display on the page.
   */
  public function getUserFromRoute($id, $status) {
    return [
      '#markup' => $this->t(
        'You have a granted access to the page @id with @status',
        ['@id' => $id, '@status' => $status],
      ),
    ];
  }

  /**
   * Redirects the user to the custom welcome page.
   */
  public function redirectRoute() {
    $response = new RedirectResponse('/custom-welcome-page');
    $response->send();
  }

}
