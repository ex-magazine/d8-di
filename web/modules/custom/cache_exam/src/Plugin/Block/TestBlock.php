<?php

namespace Drupal\cache_exam\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\UncacheableDependencyTrait;

/**
 * Provides a 'Example block for cache' Block.
 *
 * @Block(
 *   id = "cache_exam_block",
 *   admin_label = @Translation("Example block"),
 *   category = @Translation("Example block"),
 * )
 */
class TestBlock extends BlockBase {

  //use UncacheableDependencyTrait;

  public function build() {

    $node = \Drupal\node\Entity\Node::load(79);
    $node_title = $node->getTitle();

    return [
      '#markup' => $this->t('The title of node #1 is: ') . $node_title,
      '#cache' => [
        'tags' => ['node:79'],        
      ]
    ];

    /*
    $email = \Drupal::currentUser()->getEmail();

    return [
      '#markup' => $this->t('Your email is: ') . $email,
      '#cache' => [
        'contexts' => [
          'user',
        ],
      ]
    ];*/

    /*return [
      '#markup' => $this->t('Time is: ') . time(),
      '#cache' => [
        'max-age' => 0,
      ]
    ]; */
  }

  

  

}