<?php

namespace Drupal\push_notifications\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PushNotificationsConfigForm.
 *
 * @package Drupal\push_notifications\Form
 */
class PushNotificationsConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'push_notifications.PushNotificationsConfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'push_notifications_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('push_notifications.PushNotificationsConfig');
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('push_notifications.PushNotificationsConfig')
      ->save();
  }

}
