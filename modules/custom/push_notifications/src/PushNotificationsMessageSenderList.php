<?php

/**
 * @file
 * Contains \Drupal\push_notifications\PushNotificationsMessageSenderList.
 */

namespace Drupal\push_notifications;

/**
 * Send a simple message alert to an array of recipients.
 */
class PushNotificationsMessageSenderList extends PushNotificationsMessageSenderBase{

  /**
   * List of recipient user IDs.
   *
   * @var array $uids.
   */
  private $uids;

  /**
   * Constructor.
   *
   * @param array $uids Array of user ids.
   */
  public function __construct($uids) {
    $this->uids = $uids;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public function setTokens() {
    foreach ($this->uids as $uid) {
      $user_tokens = push_notification_get_user_tokens($uid);
      if (!empty($user_tokens)) {
        $this->tokens = array_merge($this->tokens, $user_tokens);
      }
    }
  }

}