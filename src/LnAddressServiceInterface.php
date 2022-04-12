<?php

namespace Drupal\lnaddress;


use Drupal\Core\Url;
use Drupal\user\UserInterface;

/**
 * Provides the LnAddressServiceInterface interface.
 */
interface LnAddressServiceInterface {

  /**
   * Gets the domain.
   *
   * @return string
   *   A string providing the domain.
   */
  public function getDomain();

  /**
   * Gets the enabled status.
   *
   * @return bool
   *   A boolean providing the enabled status.
   */
  public function getEnabled();

  /**
   * Gets the minimum sendable amount.
   *
   * @return int
   *   An integer providing the minimum sendable amount.
   */
  public function getMinSendable();

  /**
   * Gets the maximum sendable amount.
   *
   * @return int
   *   An integer providing the maximum sendable amount.
   */
  public function getMaxSendable();

  /**
   * Gets the maximum comment length.
   *
   * @return int
   *   An integer providing the maximum comment length.
   */
  public function getMaxCommentLength();

  /**
   * Gets the logging status.
   *
   * @return bool
   *   A boolean representing the logging status.
   */
  public function getLogging();

  /**
   * Saves the configuration.
   *
   * @param array $input
   *   An array of values.
   */
  public function saveConfiguration($input);

  /**
   * Resolves the username to a user object.
   *
   * @param string $username
   *   Provides the username.
   *
   * @return mixed
   */
  public function resolveUsername($username);

  /**
   * Gets the user data defaults.
   *
   * @return array
   *   An array of user data defaults.
   */
  public function getUserDataDefaults();

  /**
   * Gets the user data.
   *
   * @param int $uid
   *   Provides the user id.
   *
   * @return array
   *   An array of user data.
   */
  public function getUserData($uid);

  /**
   * Gets the pay callback url.
   *
   * @param UserInterface $username
   *   Provides the user.
   * @param $callback
   *   Provides the pa callback.
   *
   * @return Url
   *   Provides the url.
   */
  public function getPayCallbackUrl($user, $callback);

  /**
   * Gets the pay callback.
   *
   * @param UserInterface $username
   *   Provides the user.
   *
   * @return mixed
   */
  public function getUserPayCallback($user);

  /**
   * Gets the pay callback.
   *
   * @param array $conditions
   *   An array of conditions.
   *
   * @return mixed
   */
  public function getPayCallback(array $conditions = []);

  /**
   * Gets a payment request.
   *
   * @param array $props
   *   An array of input parameters.
   * @return string
   *   A string containing the payment request.
   */
  public function getPaymentRequest($props = []);

  /**
   * Performs crontab processing.
   */
  public function cron();

}
