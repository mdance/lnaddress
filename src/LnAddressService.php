<?php

namespace Drupal\lnaddress;

use Drupal\clightning\LightningServiceInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\user\UserInterface;

class LnAddressService implements LnAddressServiceInterface {

  use StringTranslationTrait;

  /**
   * Provides the config.
   */
  protected Config $config;

  /**
   * Provides the constructor method.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger service.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   The kill switch service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\clightning\LightningServiceInterface $lightning
   *   The module service.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected StateInterface $state,
    protected Connection $connection,
    protected LoggerChannelInterface $logger,
    protected KillSwitch $killSwitch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LightningServiceInterface $lightning,
  ) {
    $this->config = $configFactory->getEditable(LnAddressConstants::SETTINGS);
  }

  /**
   * {@inheritDoc}
   */
  public function getDomain(): string {
    return $this->config->get(LnAddressConstants::KEY_DOMAIN) ?? '';
  }

  /**
   * {@inheritDoc}
   */
  public function getEnabled(): bool {
    return $this->config->get(LnAddressConstants::KEY_ENABLED) ?? TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function getMinSendable(): int {
    return $this->config->get(LnAddressConstants::KEY_MIN_SENDABLE) ?? LnAddressConstants::MIN_SENDABLE;
  }

  /**
   * {@inheritDoc}
   */
  public function getMaxSendable(): int {
    return $this->config->get(LnAddressConstants::KEY_MAX_SENDABLE) ?? LnAddressConstants::MAX_SENDABLE;
  }

  /**
   * {@inheritDoc}
   */
  public function getMaxCommentLength(): int {
    return $this->config->get(LnAddressConstants::KEY_MAX_COMMENT_LENGTH) ?? LnAddressConstants::MAX_COMMENT_LENGTH;
  }

  /**
   * {@inheritDoc}
   */
  public function getLogging(): bool {
    return $this->config->get(LnAddressConstants::KEY_LOGGING) ?? TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function saveConfiguration($input): void {
    $keys = [
      LnAddressConstants::KEY_DOMAIN,
      LnAddressConstants::KEY_ENABLED,
      LnAddressConstants::KEY_MIN_SENDABLE,
      LnAddressConstants::KEY_MAX_SENDABLE,
      LnAddressConstants::KEY_MAX_COMMENT_LENGTH,
      LnAddressConstants::KEY_LOGGING,
    ];

    $save = FALSE;

    foreach ($keys as $key) {
      if (isset($input[$key])) {
        $this->config->set($key, $input[$key]);
        $save = TRUE;
      }
    }

    if ($save) {
      $this->config->save();
    }

    $keys = [
    ];

    $state = $this->state->get(LnAddressConstants::STATE, []);

    $save = FALSE;

    foreach ($keys as $key) {
      if (isset($input[$key])) {
        $state[$key] = $input[$key];
        $save = TRUE;
      }
    }

    if ($save) {
      $this->state->set(LnAddressConstants::STATE, $state);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function resolveUsername($username) {
    $output = NULL;

    $storage = $this->entityTypeManager->getStorage('user');

    $fields = [
      'name',
      'uid'
    ];

    foreach ($fields as $field) {
      $query = $storage->getQuery();

      $query->condition($field, $username);

      $results = $query->execute();

      foreach ($results as $id) {
        $output = $storage->load($id);

        return $output;
      }
    }

    return $output;
  }

  /**
   * {@inheritDoc}
   */
  public function getUserDataDefaults(): array {
    $domain = $this->getDomain();
    $enabled = $this->getEnabled();
    $min_sendable = $this->getMinSendable();
    $max_sendable = $this->getMaxSendable();
    $max_comment_length = $this->getMaxCommentLength();

    return [
      'domain' => $domain,
      'enabled' => $enabled,
      'min_sendable' => $min_sendable,
      'max_sendable' => $max_sendable,
      'metadata' => [],
      'max_comment_length' => $max_comment_length,
      'tag' => 'payRequest',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getUserData($input) {
    if (!$input instanceof UserInterface) {
      $input = $this->resolveUsername($input);
    }

    if (!$input instanceof UserInterface) {
      throw new \Exception('The user could not be resolved.');
    }

    $uid = $input->id();

    $output = $this->getUserDataDefaults();

    $a = 'a';

    $query = $this->connection->select(LnAddressConstants::TABLE_USER_DEFAULTS, $a);

    $query->fields($a);

    $query->condition('uid', $uid);

    $results = $query->execute()->fetchAll();
    $results = (array)$results;

    $output = array_merge($output, $results);

    $output['min_sendable'] = (int)$output['min_sendable'];
    $output['max_sendable'] = (int)$output['max_sendable'];
    $output['max_comment_length'] = (int)$output['max_comment_length'];

    return $output;
  }

  /**
   * {@inheritDoc}
   */
  public function getPayCallbackUrl($user, $callback = NULL) {
    $route_name = 'lnaddress.callback';
    $route_parameters = [];

    $options = [
      'absolute' => TRUE,
    ];

    if (is_null($callback)) {
      $callback = $this->getUserPayCallback($user);
    }

    $route_parameters['lnaddress_callback'] = $callback['hash'];

    $output = Url::fromRoute($route_name, $route_parameters, $options);

    return $output;
  }

  /**
   * {@inheritDoc}
   */
  public function getUserPayCallback($user) {
    $query = $this->connection->insert(LnAddressConstants::TABLE_CALLBACKS);

    $uid = $user->id();
    $time = time();
    $nonce = openssl_random_pseudo_bytes(256);
    $user_data = $this->getUserData($user);

    $fields = [];

    $fields['created'] = $time;
    $fields['uid'] = $uid;

    $data = [
      'uid' => $uid,
      'time' => $time,
      'nonce' => $nonce,
    ];
    $data = json_encode($data);

    $fields['hash'] = hash('sha256', $data);
    $fields['enabled'] = TRUE;
    $fields['min_sendable'] = $user_data['min_sendable'];
    $fields['max_sendable'] = $user_data['max_sendable'];

    $query->fields($fields);

    $fields['id'] = $query->execute();

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function getPayCallback(array $conditions = []) {
    $a = 'a';

    $query = $this->connection->select(LnAddressConstants::TABLE_CALLBACKS, $a);

    $query->fields($a);

    $defaults = [
      'field' => '',
      'value' => '',
      'operator' => '=',
    ];

    foreach ($conditions as $condition) {
      $condition = array_merge($defaults, $condition);

      $query->condition($condition['field'], $condition['value'], $condition['operator']);
    }

    $results = $query->execute();

    $output = [];

    foreach ($results as $result) {
      $output[] = (array)$result;
    }

    return $output;
  }

  /**
   * {@inheritDoc}
   */
  public function getPaymentRequest($props = []) {
    $invoice = $this->lightning->createInvoice($props);

    if ($invoice) {
      return $invoice->getBolt11();
    }
  }

  /**
   * {@inheritDoc}
   */
  public function cron() {
    // @todo Implement cron functionality
  }

}
