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
   * Android broadcaster.
   */
  protected $androidBroadcaster;

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * Send payload.
   */
  public function sendPayload() {
    dpm('Not implemented yet. sadface!');

    return;

    // Send payload to iOS recipients.
    if (!empty($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_IOS])) {
      // Convert the payload into the correct format for APNS.
      $payload_apns = array('aps' => $this->payload);
      push_notifications_apns_send_message($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_IOS], $payload_apns);
    }

    // Send payload to Android recipients.
    if (!empty($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_ANDROID])) {
      $androidBroadcaster = \Drupal::service('push_notifications.broadcaster_android');
      $androidBroadcaster->setTokens($this->tokens[PUSH_NOTIFICATIONS_TYPE_ID_ANDROID]);
      $androidBroadcaster->setPayload($this->payload);
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
