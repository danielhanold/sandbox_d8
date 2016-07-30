<?php

/**
 * @file
 * Contains \Drupal\push_notifications\PushNotificationsAlertDispatcher.
 */

namespace Drupal\push_notifications;

/**
 * Handles delivery of simple notification alert.
 */
class PushNotificationsAlertDispatcher {

  /**
   * Array of tokens grouped by type.
   */
  protected $tokens = array();

  /**
   * Message payload.
   */
  protected $payload;

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * Send payload.
   */
  public function sendPayload() {
    // Send payload to iOS recipients.
    if (!empty($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_IOS])) {
      $apnsBroadcaster = \Drupal::service('push_notifications.broadcaster_apns');
      // TODO
      // Convert the payload into the correct format for APNS.
      // $payload_apns = array('aps' => $this->payload);
      $apnsBroadcaster->setTokens($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_IOS]);
      $apnsBroadcaster->setPayload($this->payload);
      $results = $apnsBroadcaster->getResults();
      // TODO: Log results.
    }

    // Send payload to Android recipients.
    if (!empty($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_ANDROID])) {
      $androidBroadcaster = \Drupal::service('push_notifications.broadcaster_android');
      $androidBroadcaster->setTokens($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_ANDROID]);
      $androidBroadcaster->setPayload($this->payload);
      $results = $androidBroadcaster->getResults();
      // TODO: Log result message.
    }
  }

  /*
   * Setter function for payload.
   *
   * @param mixed $tokens
   */
  public function setTokens($tokens) {
    $this->tokens = $this->groupTokensByType($tokens);
  }

  /**
   * Setter method for payload.
   *
   * @param mixed $payload
   */
  public function setPayload($payload) {
    $this->payload = $payload;
  }

  /**
   * Group tokens by type.
   *
   * @param array $tokens_flat Array of token record objects.
   * @return array $tokens Array of tokens grouped by type.
   */
  private function groupTokensByType($tokens_flat = array()) {
    $tokens = array();
    foreach ($tokens_flat as $token) {
      switch ($token->type) {
        case PUSH_NOTIFICATIONS_TYPE_ID_IOS:
          $tokens[PUSH_NOTIFICATIONS_TYPE_ID_IOS][] = $token->token;
          break;

        case PUSH_NOTIFICATIONS_TYPE_ID_ANDROID:
          $tokens[PUSH_NOTIFICATIONS_TYPE_ID_ANDROID][] = $token->token;
          break;
      }
    }
    return $tokens;
  }

}
