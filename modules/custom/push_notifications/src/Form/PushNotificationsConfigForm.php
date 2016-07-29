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
    // Form constructor.
    $form = parent::buildForm($form, $form_state);

    // Get config.
    $config = $this->config('push_notifications.PushNotificationsConfig');

    $configuration_apns_replacements = array(
      '@link' => 'http://blog.boxedice.com/2009/07/10/how-to-build-an-apple-push-notification-provider-server-tutorial/',
      ':cert_name_development' => push_notifications_get_certificate_name('development'),
      ':cert_name_production' => push_notifications_get_certificate_name('production'),
    );

    $form['apns'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Apple Push Notifications'),
      '#description' => $this->t('Configure Push Notifications for Apple\'s Push Notification Server. Select your environment. Both environments require the proper certificates in the \'certificates\' folder of this module.<br />The filename for the development certificate should be \':cert_name_development\', the production certificate should be \':cert_name_production\'. See <a href="@link" target="_blank">this link</a> for instructions on creating certificates.', $configuration_apns_replacements),
    );


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
