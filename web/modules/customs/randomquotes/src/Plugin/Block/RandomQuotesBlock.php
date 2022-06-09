<?php
/**
 * @file
 * Contains \Drupal\randomquotes\Plugin\Block\RandomQuotesBlock.
 */

namespace Drupal\randomquotes\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a Random Quotes block.
 *
 * @Block(
 *   id = "randomquotes_block",
 *   admin_label = @Translation("Provides a Random Quotes block"),
 *   category = @Translation("Random Quotes")
 * )
 */
class RandomQuotesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\randomquotes\Form\RandomQuotes');
    return $form;    
  }
  

}