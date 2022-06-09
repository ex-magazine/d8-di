<?php

namespace Drupal\custom_site_settings\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the SettingForm form controller.
 * @see \Drupal\Core\Form\FormBase
 */
class SettingForm extends FormBase {

  /**
   * Build the simple form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = \Drupal::config('system.site');   

    $form['sitename'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site name'),
      '#default_value' => $config->get('name'),
      '#description' => $this->t('Site names with less than 6 characters length'),
      '#required' => TRUE,
    ];
   
    $form['actions'] = [
      '#type' => 'actions',
    ];
   
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Getter method for Form ID.   
   */
  public function getFormId() {
    return 'custom_site_settings_site_form';
  }

  /**
   * Implements form validation.  
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $sitename = $form_state->getValue('sitename');
    if (strlen($sitename) > 6) {      
      $form_state->setErrorByName('sitename', $this->t('Site names with less than 6 characters length.'));
    }
  }

  /**
   * Implements a form submit handler.  
   */
  public function submitForm(array &$form, FormStateInterface $form_state) { 
    $sitename = $form_state->getValue('sitename');
    $this->messenger()->addMessage($this->t('The Site name is %sitename.', ['%sitename' => $sitename]));
  }

}
