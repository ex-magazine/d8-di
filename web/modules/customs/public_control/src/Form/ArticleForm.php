<?php

namespace Drupal\public_control\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Implements ArticleForm form controller.
 */
class ArticleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('When selecting an article and clicking Update, Article publishing options should be changed accordingly'),
    ];
    
    $array_article = $this->getArticle();        

    $form['article'] = [
      '#type' => 'select',
      '#title' => $this->t('Article Title'),
      '#options' =>   $array_article,  
      '#required' => TRUE,    
      //'#default_value' => $default_option,
      '#empty_option' => $this->t('-select-'),
      '#description' => $this->t('Select a article title'),
    ];   

    $form['status_publish'] = [
      '#type' => 'select',
      '#title' => $this->t('Publish Status'),
      '#options' => [
        '1' => $this->t('Publish'),
        '0' => $this->t('Un Publish'),        
      ],            
    ];

    $form['status_sticky'] = [
      '#type' => 'select',
      '#title' => $this->t('Sticky Status'),
      '#options' => [
        '1' => $this->t('Sticky'),
        '0' => $this->t('Un Sticky'),        
      ],      
    ];   

    $form['publish'] = array(
      '#type' => 'submit',
      '#value' => t('Update'),
      '#submit' => array('::newSubmissionHandlerPublish'),
    );
    
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#submit' =>  array([$this, 'newSubmissionHandlerDelete']), 
      
    );
  
    return $form;
  }

  public function getArticle() {
    $arr = array();
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'article');
      // ->condition('status', 1)  
      // ->sort('nid', ASC)
      // ->range(0, 100);
     //->accessCheck(FALSE);
    $results = $query->execute();    
  
    foreach ($results as $key => $value) {
      $node = Node::load($value);  
      $arr[$value] = $node->title->value;
      //array_push($arr,  $node->title->value);
    }
    return $arr;    
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'public_control_form_api';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) { 
    //expert do here
    
  }

  public function newSubmissionHandlerPublish(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $article_id = $values['article'];
    $publish_id = $values['status_publish'];
    $sticky_id = $values['status_sticky'];    

    $node = \Drupal::entityTypeManager()->getStorage('node')->load($article_id);
    if ($node) {     
      if ($publish_id == 1) {
        $node->setPublished(true);
      } else {
        $node->setPublished(false);
      }        
      if ($sticky_id == 1) {
        $node->setSticky(true);
      } else {
        $node->setSticky(false);
      }
      $node->save();
    }     

    $message = $this->t('Submit for %article_id', ['%article_id' => $article_id]);
    $this->messenger()->addMessage($message);
    $form_state->setRebuild();    
  }

  public function newSubmissionHandlerDelete(array &$form, FormStateInterface $form_state) {
    
    $values = $form_state->getValues();
    $article_id = $values['article'];
    $publish_id = $values['status_publish'];
    $sticky_id = $values['status_sticky'];    
    
    $node = Node::load($article_id);     
    if ($node) {
      $node->delete();
    } 

    $message = $this->t('Delete for %article_id', ['%article_id' => $article_id]);
    $this->messenger()->addMessage($message);
    $form_state->setRebuild();    
  }
}
