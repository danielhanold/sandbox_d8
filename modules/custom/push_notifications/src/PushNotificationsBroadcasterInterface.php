<?php

/**
 * @file
 * Contains \Drupal\push_notifications\PushNotificationsBroadcasterInterface.
 */

interface PushNotificationsBroadcasterInterface {

  /**
   * Broadcast message.
   *
   * @param array $tokens Token list.
   * @param array $payload Payload to be sent.
   */
  function broadcastMessage();

  /**
   * Set tokens.
   *
   * @param array $tokens Token list.
   */
  function setTokens($tokens);

  /**
   * Set payload.
   *
   * @param array $payload Payload.
   */
  function setPaylod($payload);

  /**
   * Retrieve results after broadcast was sent.
   *
   * @return array Array of data.
   */
  function getResults();
}