<?php

/**
 * @file
 * Contains Drupal\randomquotes\Games\ShowQuotes.
 */

namespace Drupal\randomquotes\Games;

use Drupal\Core\Config\ConfigFactory;
use RandomQuotes\RandomQuotes;


/**
 * Class ShowQuotes. 
 */
class ShowQuotes {

  protected $quotes;    

  public function __construct() {
    $quote = new RandomQuotes();
    $this->quotes = $quote->generate();
  }


  public function getQuotesValue() {
    return $this->quotes;
  }  

}