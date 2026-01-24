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
  public function getDomain(): string;

  /**
   * Gets the enabled status.
   *
   * @return bool
   *   A boolean providing the enabled status.
   */
  public function getEnabled(): bool;

  /**
   * Gets the minimum sendable amount.
   *
   * @return int
   *   An integer providing the minimum sendable amount.
   */
  public function getMinSendable(): int;

  /**
   * Gets the maximum sendable amount.
   *
   * @return int
   *   An integer providing the maximum sendable amount.
   */
  public function getMaxSendable(): int;

  /**
   * Gets the maximum comment length.
   *
   * @return int
   *   An integer providing the maximum comment length.
   */
  public function getMaxCommentLength(): int;

  /**
   * Gets the logging status.
   *
   * @return bool
   *   A boolean representing the logging status.
   */
  public function getLogging(): bool;

  /**
   * Saves the configuration.
   *
   * @param array $input
   *   An array of values.
   *
   * @return self
   */
  public function saveConfiguration(array $input): self;

  /**
   * Resolves the username to a user object.
   *
   * @param string $username
   *   Provides the username.
   *
   * @return ?UserInterface
   *   The user object, otherwise NULL.
   */
  public function resolveUsername(string $username): ?UserInterface;

  /**
   * Gets the user data defaults.
   *
   * @return array
   *   An array of user data defaults.
   */
  public function getUserDataDefaults(): array;

  /**
   * Gets the user data.
   *
   * @param int|UserInterface $user
   *   Provides the user id.
   *
   * @return array
   *   An array of user data.
   */
  public function getUserData(int|UserInterface $user): array;

  /**
   * Gets the pay callback url.
   *
   * @param UserInterface $user
   *   Provides the user.
   * @param ?array $callback
   *   An optional callback.
   *
   * @return Url
   *   Provides the url.
   */
  public function getPayCallbackUrl(
    UserInterface $user,
    ?array $callback = NULL,
  ): Url;

  /**
   * Gets the pay callback.
   *
   * @param UserInterface $user
   *   The user.
   *
   * @return ?array
   *   The user pay callback.
   */
  public function getUserPayCallback(UserInterface $user): ?array;

  /**
   * Gets the pay callback.
   *
   * @param array $conditions
   *   An array of conditions.
   *
   * @return array
   *   The pay callback.
   */
  public function getPayCallback(array $conditions = []): array;

  /**
   * Gets a payment request.
   *
   * @param array $props
   *   An array of input parameters.
   * @return string|bool
   *   A string containing the payment request, or FALSE.
   */
  public function getPaymentRequest(array $props = []): string|bool;

  /**
   * Performs crontab processing.
   */
  public function cron(): void;

}
