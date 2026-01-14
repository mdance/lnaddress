<?php

namespace Drupal\lnaddress\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\lnaddress\LnAddressConstants;
use Drupal\lnaddress\LnAddressServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides the LnAddressController controller.
 */
class LnAddressController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Provides the constructor.
   *
   * @param \Drupal\lnaddress\LnAddressServiceInterface $lnaddress
   *   Provides the module service.
   */
  public function __construct(
    protected LnAddressServiceInterface $lnaddress,
  ) {
  }

  /**
   * Provides the lightning url pay route.
   *
   * @param string $username
   *   Provides the username.
   *
   * @return JsonResponse
   *   The JSON response.
   */
  public function lnUrlPay($username) {
    $output = new JsonResponse();

    $data = [];
    $error = FALSE;
    $reason = NULL;

    $user = $this->lnaddress->resolveUsername($username);
    $user_data = $this->lnaddress->getUserData($user);

    $domain = $user_data[LnAddressConstants::KEY_DOMAIN];
    $enabled = $user_data[LnAddressConstants::KEY_ENABLED];
    $min_sendable = $user_data[LnAddressConstants::KEY_MIN_SENDABLE];
    $max_sendable = $user_data[LnAddressConstants::KEY_MAX_SENDABLE];
    $max_comment_length = $user_data[LnAddressConstants::KEY_MAX_COMMENT_LENGTH];

    if (!$enabled) {
      $error = TRUE;
      $reason = $this->t('Lightning Address payments are disabled for this account.');
    }

    try {
      $callback = $this->lnaddress->getPayCallbackUrl($user)->toString();

      // @todo Set these values properly
      $metadata = [
        [
          'text/plain',
          "$username@$domain",
        ],
        [
          'text/email',
          "$username@$domain",
        ],
        [
          'text/identifier',
          "$username@$domain",
        ],
        [
          'text/long-desc',
          "Lightning Address Payment",
        ],
        /*
        [
          'image/png;base64',
          '',
        ]
        [
        'image/jpeg;base64',
          '',
        ]
        */
      ];

      $metadata = json_encode($metadata);

      $data['callback'] = $callback;
      $data['maxSendable'] = $max_sendable;
      $data['minSendable'] = $min_sendable;
      $data['metadata'] = $metadata;
      $data['commentAllowed'] = $max_comment_length;
      // @todo Implement withdraw link functionality
      $data['withdrawLink'] = '';
      $data['tag'] = 'payRequest';
    } catch (\Exception $e) {
      $error = TRUE;
      $reason = $this->t('An error occurred getting a payment callback');
    }

    if ($error) {
      $data = [
        'status' => 'ERROR',
        'reason' => $reason,
      ];
    }

    $output->setData($data);

    return $output;
  }

  /**
   * Provides the callback route.
   *
   * @param string $lnaddress_callback
   *   Provides the callback.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function callback($lnaddress_callback, Request $request) {
    $output = new JsonResponse();

    $data = [];
    $error = FALSE;
    $reason = NULL;

    $amount = $request->query->get('amount');

    if (!is_numeric($amount)) {
      $error = TRUE;
      $reason = $this->t('Please specify an amount.');
    }

    $comment = $request->query->get('comment') ?? '';
    $nonce = $request->query->get('nonce') ?? NULL;
    $from_nodes = $request->query->get('fromnodes') ?? NULL;
    $proof_of_payer = $request->query->get('proofofpayer') ?? NULL;

    $conditions = [];

    $conditions[] = [
      'field' => 'hash',
      'value' => $lnaddress_callback,
    ];

    $callbacks = $this->lnaddress->getPayCallback($conditions);
    $total = count($callbacks);

    if (!$total) {
      $error = TRUE;
      $reason = $this->t('The payment callback was not found');
    } else {
      $callback = array_shift($callbacks);

      $uid = $callback['uid'];

      $user_data = $this->lnaddress->getUserData($uid);

      $enabled = $user_data['enabled'];
      $min_sendable = $user_data['min_sendable'];
      $max_sendable = $user_data['max_sendable'];
      $max_comment_length = $user_data['max_comment_length'];

      if (!$enabled) {
        $error = TRUE;
        $reason = $this->t('Lightning Address payments are disabled for this account.');
      }

      if ($min_sendable != 0 && $amount < $min_sendable) {
        $error = TRUE;

        $args = [];

        $args['@value'] = $min_sendable;

        $reason = $this->t('The minimum amount is @value', $args);
      }

      if ($max_sendable != 0 && $amount > $max_sendable) {
        $error = TRUE;

        $args = [];

        $args['@value'] = $max_sendable;

        $reason = $this->t('The maximum amount is @value', $args);
      }

      if ($max_comment_length != 0 && strlen($comment) > $max_comment_length) {
        $error = TRUE;

        $args = [];

        $args['@value'] = $max_comment_length;

        $reason = $this->t('The maximum comment length is @value', $args);
      }

      try {
        $props = [];

        $props['amount'] = $amount;
        $props['description'] = $this->t('Lightning Address Payment');

        $metadata = [];

        $metadata['uid'] = $uid;
        $metadata['lnaddress_callback'] = $lnaddress_callback;

        $props['metadata'] = $metadata;

        $pr = $this->lnaddress->getPaymentRequest($props);

        $data['pr'] = $pr;
        // @todo Implement success action functionality.
        $data['successAction'] = NULL;
        // @todo Implement disposable functionality.
        $data['disposable'] = FALSE;
        // @todo Implement routes functionality.
        $data['routes'] = [];
      } catch (\Exception $e) {
        $error = TRUE;
        $reason = $this->t('An error occurred retrieving a payment request.');
      }
    }

    if ($error) {
      $data = [
        'status' => 'ERROR',
        'reason' => $reason,
      ];
    }

    $output->setData($data);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('lnaddress')
    );
  }

}
