<?php

namespace Drupal\custom_block_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block module example block.
 *
 * @Block(
 *   id = "custom_block_module_block_module_example",
 *   admin_label = @Translation("Block Module Example"),
 *   category = @Translation("Custom")
 * )
 */
class BlockModuleExampleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
