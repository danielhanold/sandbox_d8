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
   * @param \Drupal\push_notifications\PushNotificationsAlertDispatcher $dispatcher
   *   Alert Dispatcher.
   */
  public function __construct(PushNotificationsAlertDispatcher $dispatcher) {
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   *
   * @param array $uids
   */
  public function setRecipients($uids) {
    $this->uids = $uids;
  }

  /**
   * {@inheritdoc}
   */
  public function generateTokens() {
    foreach ($this->uids as $uid) {
      $user_tokens = push_notification_get_user_tokens($uid);
      if (!empty($user_tokens)) {
        $this->tokens = array_merge($this->tokens, $user_tokens);
      }
    }
  }

}