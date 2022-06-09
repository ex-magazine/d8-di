<?php


namespace Drupal\randomquotes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
/**
 * Implements an RandomQuotes form.
 */
class RandomQuotes extends FormBase {

  protected $quotesService;  
  
  /**
   * {@inheritdoc}.
   */
  public function __construct($quotesService) {
    $this->quotesService = $quotesService;
  }

  
  /**
   * {@inheritdoc}.
   */

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('randomquotes.shows')
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'randomquotes_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {  

    $form['quotes_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Random Quotes:'),
      '#required' => TRUE,
      '#description' => $this->t('This text will appear random'),
      '#default_value' => $this->quotesService->getQuotesValue(),          
    );   
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );
    
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('@quotes_name ,Your Quotes is being submitted!', array('@quotes_name' => $form_state->getValue('quotes_name'))));
  }
}

