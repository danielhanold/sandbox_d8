<?php

/**
 * @file
 * Contains \Drupal\push_notifications\PushNotificationsBroadcasterApns.
 */

namespace Drupal\push_notifications;

use Drupal\push_notifications\PushNotificationsBroadcasterInterface;

/**
 * Broadcasts Android messages.
 */
class PushNotificationsBroadcasterApns implements PushNotificationsBroadcasterInterface {

  /**
   * @var array $tokens
   *   List of tokens.
   */
  protected $tokens;

  /**
   * @var array $payload
   *   Payload.
   */
  protected $payload;

  /**
   * @var int $countAttempted
   *   Count of attempted tokens.
   */
  protected $countAttempted;

  /**
   * @var int $countSuccess
   *   Count of successful tokens.
   */
  protected $countSuccess;

  /**
   * @var bool $success
   *   Flag to indicate success of all batches.
   */
  protected $success;

  /**
   * @var string $statusMessage
   *   Status messages.
   */
  protected $message;

  /**
   * @var stream $apns
   *   APNS connection.
   */
  protected $apns;

  /**
   * @var string $certificate_path
   *   Absolute certificate path.
   */
  private $certificate_path;

  /**
   * @var object $config
   *   APNS configuration object.
   */
  private $config;


  /**
   * Constructor.
   */
  public function __construct() {
    dpm('Apns Alert Dispatcher');
    $this->config = \Drupal::config('push_notifications.apns');
    $this->determineCertificatePath();
  }

  /**
   * Setter function for tokens.
   *
   * @param $tokens
   */
  public function setTokens($tokens) {
    $this->tokens = $tokens;
  }

  /**
   * Setter function for payload.
   *
   * @param $payload
   */
  public function setPayload($payload) {
    $this->payload = $payload;
  }

  /**
   * Open a stream connection to APNS.
   * Should be closed by calling fclose($connection) after usage.
   */
  private function openConnection() {
    // Create a stream context.
    $stream_context = stream_context_create();

    // Set options on the stream context.
    stream_context_set_option($stream_context, 'ssl', 'local_cert', $this->certificate_path);

    // If the user has a passphrase stored, we use it.
    if (strlen($this->config->get('passphrase'))) {
      stream_context_set_option($stream_context, 'ssl', 'passphrase', $this->config->get('passphrase'));
    }
    if ($this->config->get('set_entrust_certificate')) {
      stream_context_set_option($stream_context, 'ssl', 'CAfile', drupal_get_path('module', 'push_notifications') . '/certificates/entrust_2048_ca.cer');
    }

    // Open an Internet socket connection.
    $this->apns = stream_socket_client('ssl://' . PUSH_NOTIFICATIONS_APNS_HOST . ':' . PUSH_NOTIFICATIONS_APNS_PORT, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
    if (!$this->apns) {
      \Drupal::logger('push_notifications')->error("Could not establish connection to Apple Notification Server failed.");
      throw new \Exception('APNS connection could not be established. Check to make sure you are using a valid certificate file.');
    }
  }

  /**
   * Determine the realpath to the APNS certificate.
   *
   * @see http://stackoverflow.com/questions/809682
   * @throws \Exception
   *   Certificate file needs to be set
   */
  private function determineCertificatePath() {
    // Determine if custom path is set.
    $path = $this->config->get('certificate_folder');

    // If no custom path is set, get module directory.
    if (empty($path)) {
      $path = drupal_realpath(drupal_get_path('module', 'push_notifications'));
      $path .= DIRECTORY_SEPARATOR . 'certificates' . DIRECTORY_SEPARATOR;
    }

    // Append name of certificate.
    $path .= push_notifications_get_certificate_name($this->config->get('environment'));

    if (!file_exists($path)) {
      \Drupal::logger('push_notifications')->error("Cannot find apns certificate file at @path", array(
        '@path' => $path,
      ));
      throw new \Exception('Could not find APNS certificate at given path.');
    }

    $this->certificate_path = $path;
  }

  /**
   * Send the broadcast message.
   *
   * @throws \Exception
   *   Array of tokens and payload necessary to send out a broadcast.
   */
  public function sendBroadcast() {
    if (empty($this->tokens) || empty($this->payload)) {
      throw new \Exception('No tokens or payload set.');
    }

    // Send a push notification to every recipient.
    $stream_counter = 0;
    foreach ($this->tokens as $token) {
      // Open an apns connection, if necessary.
      if ($stream_counter == 0) {
        $this->openConnection();
      }
      $stream_counter++;

      $this->countAttempted++;
      $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', $token) . pack('n', strlen($this->paylod)) . $this->paylod;
      // Write the payload to the currently active streaming connection.
      $success = fwrite($this->apns, $apns_message);
      if ($success) {
        $this->countSuccess++;
      }
      elseif ($success == 0 || $success == FALSE || $success < strlen($apns_message)) {
        \Drupal::logger('push_notifications')->notice("APNS message could not be sent. Token: @token. fwrite returned @success_message", array(
          '@token' => $token,
          '@success_message' => $success,
        ));
      }

      // Reset the stream counter if no more messages should
      // be sent with the current stream context.
      // This results in the generation of a new stream context
      // at the beginning of this loop.
      if ($stream_counter >= PUSH_NOTIFICATIONS_APNS_STREAM_CONTEXT_LIMIT) {
        $stream_counter = 0;
        if (is_resource($this->apns)) {
          fclose($this->apns);
        }
      }
    }

    // Close the apns connection if it hasn't already been closed.
    // Need to check if $apns is a resource, as pointer will not
    // be closed by fclose.
    if (is_resource($this->apns)) {
      fclose($this->apns);
    }

    // Mark success as true.
    $this->success = TRUE;
  }

  /**
   * Get the results of a batch.
   */
  public function getResults() {
    return array(
      'type_id' => PUSH_NOTIFICATIONS_TYPE_ID_IOS,
      'payload' => $this->payload,
      'count_attempted' => $this->countAttempted,
      'count_success' => $this->countSuccess,
      'success' => $this->success,
    );
  }

}