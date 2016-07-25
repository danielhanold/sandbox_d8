<?php

/**
 * @file
 * Contains \Drupal\push_notifications\PushNotificationsMessageSenderBase.
 */

namespace Drupal\push_notifications;

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
   */
  protected $payload;

  /**
   * List of tokens that should be sent.
   */
  protected $tokens;

  /**
   * Constructor.
   *
   * @param array $recipients Array of user ids.
   * @param string $message Message to be included in payload.
   */
  protected function __construct($recipients, $message) {
    if (!is_array($recipients) || !is_string($message)) {
      throw new Exception('Recipient or message have an incorrect format.');
    }

    // Allow other modules modify the message before it is sent.
    $implementations = \Drupal::moduleHandler()
      ->getImplementations('push_notifications_send_message');
    foreach ($implementations as $module) {
      $function = $module . '_push_notifications_send_message';
      $function($message, $type = 'simple');
    }

    $this->message = $this->truncateMessage($message);
    $this->generatePayload();

  }

  /**
   * Determine the list of tokens.
   */
  protected abstract function getRecipients();

  /**
   * Function to truncate message. Ensures that the message will be sent
   * out correctly on APNS.
   *
   * @param string $message Message that will be truncated.
   */
  protected function truncateMessage($message) {
    return truncate_utf8($message, PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT, TRUE, TRUE);
  }

  /**
   * Generate the payload in the correct format for delivery.
   */
  protected function generatePayload() {
    $this->payload = array('alert' => $this->message);
  }

}