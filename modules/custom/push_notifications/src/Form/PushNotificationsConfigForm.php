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
    $config_apns = $this->config('push_notifications.apns');
    $config_gcm = $this->config('push_notifications.gcm');

    $configuration_apns_replacements = array(
      '@link' => 'http://blog.boxedice.com/2009/07/10/how-to-build-an-apple-push-notification-provider-server-tutorial/',
      ':cert_name_development' => push_notifications_get_certificate_name('development'),
      ':cert_name_production' => push_notifications_get_certificate_name('production'),
    );

    // APNS.
    $form['apns'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Apple Push Notifications'),
      '#description' => $this->t('Configure Push Notifications for Apple\'s Push Notification Server. Select your environment. Both environments require the proper certificates in the \'certificates\' folder of this module.<br />The filename for the development certificate should be \':cert_name_development\', the production certificate should be \':cert_name_production\'. See <a href="@link" target="_blank">this link</a> for instructions on creating certificates.', $configuration_apns_replacements),
    );

    $form['apns']['regenerate_certificate_string_description'] = array(
      '#type' => 'item',
      '#title' => $this->t('APNS Certificate Name'),
      '#markup' => $this->t('Click here to create a new random name for your APNS certificates. Please note that you will have to update the filenames for both certificate files accordingly.'),
    );

    $form['apns']['regenerate_certificate_string'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Generate new certificate string'),
      '#submit' => array('push_notifications_regenerate_certificate_string_submit'),
    );

    $form['apns']['push_notifications_apns_environment'] = array(
      '#type' => 'select',
      '#title' => $this->t('APNS Environment'),
      '#description' => $this->t('Select the active APNS Environment. Please note that development certificates do not work with apps released in the Apple app store; production certificates only work with apps released in the app store.'),
      '#options' => array(
        'development' => 'Development',
        'production' => 'Production',
      ),
      '#default_value' => $config_apns->get('environment'),
    );

    $stream_context_limit_options = array(1, 5, 10, 25, 50);
    $form['apns']['stream_context_limit'] = array(
      '#type' => 'select',
      '#title' => $this->t('Stream Context Limit'),
      '#description' => $this->t('Defines the amount of messages sent per stream limit, i.e. how many notifications are sent per connection created with Apple\'s servers. The higher the limit, the faster the message delivery. If the limit is too high, messages might not get delivered at all. Unclear (to me) what Apple\'s <em>actual</em> limit is.'),
      '#options' => array_combine($stream_context_limit_options, $stream_context_limit_options),
      '#default_value' => $config_apns->get('stream_context_limit'),
    );

    $form['apns']['passphrase'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Passphrase'),
      '#description' => $this->t('If your APNS certificate has a passphrase, enter it here. Otherwise, leave this field blank.'),
      '#default_value' => $config_apns->get('passphrase'),
    );

    $form['apns']['push_notifications_apns_certificate_folder'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('APNS Certificate Folder Path'),
      '#description' => $this->t('The preferred location for the certificate files is a folder outside of your web root, i.e. a folder not accessible through the Internet. Specify the full path here, e.g. \'/users/danny/drupal_install/certificates/\'. If you are using the \'certificates\' folder within the module directory, leave this field blank.'),
      '#default_value' => $config_apns->get('certificate_folder'),
    );

    $form['apns']['push_notifications_set_entrust_certificate'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Set Entrust root certificate'),
      '#description' => t('If APNS does not work and you are getting errors like %php_error_string your server might be missing the Entrust root certificate. Enable this to explicitely add it when establishing a connection to APNS. See more in this <a href="@link_kb" target="_blank">Knowledgebase Article</a> and the <a href="@link_entrus" target="_blank">Entrust Root Certificate Downloads</a>', array(
        '%php_error_string' => 'Warning: stream_socket_client() [...]',
        '@link_entrust' => 'https://www.entrust.com/get-support/ssl-certificate-support/root-certificate-downloads/',
        '@link_kb' => 'http://stackoverflow.com/questions/4817520/why-ssl-of-entrust-ssl-certificate-is-required-for-apns',
      )),
      '#default_value' => $config_apns->get('set_entrust_certificate'),
    );

    // Google Cloud Messaging.
    $form['gcm'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Google Cloud Messaging'),
      '#description' => $this->t('Enter your Google Cloud Messaging details.'),
    );

    $form['gcm']['api_key'] = array(
      '#type' => 'textfield',
      '#title' => t('Google Cloud Messaging API Key'),
      '#description' => t('Enter the API key for your Google Cloud project'),
      '#default_value' => $config_gcm->get('api_key'),
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
