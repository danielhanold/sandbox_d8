<?php

/**
 * @file
 * Contains \Drupal\push_notifications\PushNotificationsMessageSenderBase.
 */

namespace Drupal\push_notifications;

use Drupal\Component\Utility\Unicode;

/**
 * Handles sending of alerts.
 */
abstract class PushNotificationsMessageSenderBase{

  /**
   * The message that will be used in the payload.
   *
   * @var string $message
   */
  protected $message;

  /**
   * The payload containing the message.
   *
   * @var array $payload.
   */
  protected $payload;

  /**
   * List of user tokens.
   *
   * @var array $tokens
   */
  protected $tokens = array();

  /**
   * Constructor.
   */
  protected function __construct() {
    $this->setTokens();
  }

  /**
   * Set the list of tokens for this target. Needs to be an associative
   * array of user tokens with the token as the array key.
   */
  abstract public function setTokens();

  /**
   * Setter function for message.
   *
   * @param string $message Message to send.
   */
  public function setMessage($message) {
    if (!is_string($message)) {
      throw new \Exception('Message needs to be a string.');
    }

    // Allow other modules modify the message before it is sent.
    $implementations = \Drupal::moduleHandler()
      ->getImplementations('push_notifications_send_message');
    foreach ($implementations as $module) {
      $function = $module . '_push_notifications_send_message';
      $function($message, $type = 'simple');
    }

    // Truncate the message so that we don't exceed the limit of APNS.
    $this->message = Unicode::truncate($message, PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT, TRUE, TRUE);
  }

  /**
   * Generate the payload in the correct format for delivery.
   */
  protected function generatePayload() {
    $this->payload = array('alert' => $this->message);
  }

  /**
   * Dispatch an alert.
   */
  public function dispatchAlert() {
    // Verify that message is set.
    if (empty($this->message)) {
      throw new \Exception('Message was not set correctly.');
    }

    // Log message if no tokens are available.
    if (empty($this->tokens)) {
      \Drupal::logger('push_notifications')->notice('No tokens found.');
      return false;
    }

    // Generate the payload.
    $this->generatePayload();

    // TODO: Use the alert dispatcher class to send out an alert.
  }

}