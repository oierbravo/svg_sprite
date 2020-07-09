<?php

namespace Drupal\svg_sprite\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AdminConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'svg_sprite_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'svg_sprite.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('svg_sprite.settings');
    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('File path'),
      '#default_value' => $config->get('path'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('svg_sprite.settings')
      ->set('path', $form_state->getValue('path'))
      ->save();
    parent::submitForm($form, $form_state);
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $path = $form_state->getValue('path');
    if(!file_exists($path)){
      $form_state->setErrorByName('path',$this->t('File not accesible.'));
    }
    parent::validateForm($form, $form_state);
  }

}
