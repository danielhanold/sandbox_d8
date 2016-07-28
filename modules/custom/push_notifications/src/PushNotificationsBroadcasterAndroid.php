<?php

/**
 * @file
 * Contains \Drupal\push_notifications\PushNotificationsBroadcasterAndroid.
 */

namespace Drupal\push_notifications;
use Drupal\push_notifications\PushNotificationsBroadcasterInterface;

/**
 * Broadcasts Android messages.
 */
class PushNotificationsBroadcasterAndroid implements PushNotificationsBroadcasterInterface {

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
   * Constructor.
   */
  public function __construct() {
    dpm('Android Alert Dispatcher');
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
   * Get the headers for sending broadcast.
   */
  private function getHeaders() {
    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Authorization:key=' . \Drupal::config('push_notifications.gcm')->get('api_key');
    return $headers;
  }
}