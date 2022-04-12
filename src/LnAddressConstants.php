<?php

namespace Drupal\lnaddress;

/**
 * Provides the LnAddressConstants class.
 */
class LnAddressConstants {

  /**
   * Provides the settings.
   *
   * @var string
   */
  const SETTINGS = 'lnaddress.settings';

  /**
   * Provides the state namespace.
   */
  const STATE = 'lnaddress';

  /**
   * Provides the admin permission.
   *
   * @var string
   */
  const PERMISSION_ADMIN = 'administer lnaddress';

  /**
   * Provides the login route.
   *
   * @var string
   */
  const ROUTE_LNURL_PAY = 'lnaddress.lnurl_pay';

  /**
   * Provides the minimum sendable amount.
   *
   * @var int
   */
  const MIN_SENDABLE = 1000;

  /**
   * Provides the maximum sendable amount.
   *
   * @var int
   */
  const MAX_SENDABLE = 4696729000;

  /**
   * Provides the maximum comment length.
   *
   * @var int
   */
  const MAX_COMMENT_LENGTH = 1000;

  /**
   * Provides the user defaults table.
   *
   * @var string
   */
  const TABLE_USER_DEFAULTS = 'lnaddress_user_defaults';

  /**
   * Provides the callbacks table.
   */
  const TABLE_CALLBACKS = 'lnaddress_callbacks';

  /**
   * Provides the domain key.
   */
  const KEY_DOMAIN = 'domain';

  /**
   * Provides the enabled key.
   */
  const KEY_ENABLED = 'enabled';

  /**
   * Provides the minimum sendable key.
   */
  const KEY_MIN_SENDABLE = 'min_sendable';

  /**
   * Provides the maximum sendable key.
   */
  const KEY_MAX_SENDABLE = 'max_sendable';

  /**
   * Provides the maximum comment length.
   */
  const KEY_MAX_COMMENT_LENGTH = 'max_comment_length';

  /**
   * Provides the logging key.
   */
  const KEY_LOGGING = 'logging';

}
