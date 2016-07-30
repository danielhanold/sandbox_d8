<?php

namespace Drupal\push_notifications\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PushNotificationsSendMessageForm.
 *
 * @package Drupal\push_notifications\Form
 */
class PushNotificationsSendMessageForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'send_message_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['instructions'] = array(
      '#type' => 'item',
      '#markup' => $this->t('Compose the elements of your push notification message.'),
    );


    $form['message'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Push Message'),
      '#description' => $this->t('Compose the message to send out (@limit characters max.)', array(
        '@limit' => PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT,
      )),
      '#required' => TRUE,
      '#size' => 128,
      '#maxlength' => PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT,
    );

    // Only show Android option if GCM Api Key is available..
    $recipients_options = array('ios' => t('iOS (Apple Push Notifications)'));
    if (!empty(\Drupal::config('push_notifications.gcm')->get('api_key'))) {
      $recipients_options['android'] = t('Android (Google Cloud Messaging)');
    }
    $form['recipients'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Target Networks'),
      '#description' => t('Select the networks you want to reach with this message.'),
      '#options' => $recipients_options,
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Send Push Notification'),
    );

    return $form;
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
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
