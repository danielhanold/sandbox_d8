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
   *
   * @param array $tokens Array of token record objects.
   * @param array $payload Payload.
   */
  public function __construct($tokens = array(), $payload = array()) {
    $this->payload = $payload;
    $this->tokens = $this->groupTokensByType($tokens);

    // Send payload to iOS recipients.
    if (!empty($tokens[PUSH_NOTIFICATIONS_TYPE_ID_IOS])) {
      // Convert the payload into the correct format for APNS.
      $payload_apns = array('aps' => $payload);
      push_notifications_apns_send_message($tokens[PUSH_NOTIFICATIONS_TYPE_ID_IOS], $payload_apns);
    }

    // Send payload to Android recipients.
    if (!empty($tokens[PUSH_NOTIFICATIONS_TYPE_ID_ANDROID])) {
      push_notifications_gcm_send_message($tokens[PUSH_NOTIFICATIONS_TYPE_ID_ANDROID], $payload);
    }
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
