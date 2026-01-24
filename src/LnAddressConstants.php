<?php

namespace Drupal\lnaddress;

/**
 * Provides the LnAddressConstants class.
 */
class LnAddressConstants {

  /**
   * Provides the settings.
   */
  const string SETTINGS = 'lnaddress.settings';

  /**
   * Provides the state namespace.
   */
  const string STATE = 'lnaddress';

  /**
   * Provides the admin permission.
   */
  const string PERMISSION_ADMIN = 'administer lnaddress';

  /**
   * Provides the login route.
   */
  const string ROUTE_LNURL_PAY = 'lnaddress.lnurl_pay';

  /**
   * Provides the minimum sendable amount.
   */
  const int MIN_SENDABLE = 1000;

  /**
   * Provides the maximum sendable amount.
   */
  const int MAX_SENDABLE = 4696729000;

  /**
   * Provides the maximum comment length.
   */
  const int MAX_COMMENT_LENGTH = 1000;

  /**
   * Provides the user defaults table.
   */
  const string TABLE_USER_DEFAULTS = 'lnaddress_user_defaults';

  /**
   * Provides the callbacks table.
   */
  const string TABLE_CALLBACKS = 'lnaddress_callbacks';

  /**
   * Provides the domain key.
   */
  const string KEY_DOMAIN = 'domain';

  /**
   * Provides the enabled key.
   */
  const string KEY_ENABLED = 'enabled';

  /**
   * Provides the minimum sendable key.
   */
  const string KEY_MIN_SENDABLE = 'min_sendable';

  /**
   * Provides the maximum sendable key.
   */
  const string KEY_MAX_SENDABLE = 'max_sendable';

  /**
   * Provides the maximum comment length.
   */
  const string KEY_MAX_COMMENT_LENGTH = 'max_comment_length';

  /**
   * Provides the logging key.
   */
  const string KEY_LOGGING = 'logging';

}
