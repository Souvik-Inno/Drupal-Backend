<?php declare(strict_types = 1);

namespace Drupal\block_api\Plugin\Block;

use Drupal\block_api\UserRole;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a welcome block.
 *
 * @Block(
 *   id = "block_api_welcome",
 *   admin_label = @Translation("Welcome"),
 *   category = @Translation("Custom"),
 * )
 */
class WelcomeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   * 
   * @var \Drupal\block_api\UserRole
   */
  protected $user;
  
  /**
   * Contructs an object of te class.
   */
  public function __construct(
    array $configuration, 
    $plugin_id, 
    $plugin_definition, 
    protected UserRole $currentUser,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->user = $currentUser;
  }
  
  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('block_api.user_role'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $role = $this->user->getUserRole();
    $build['content'] = [
      '#markup' => $this->t('Welcome @role', ['@role' => $role]),
      '#cache' => [
        'tags' => $this->user->getUserCacheTags(),
      ],
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIf(TRUE);
  }

}
