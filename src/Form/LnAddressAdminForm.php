<?php

namespace Drupal\lnaddress\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\lnaddress\LnAddressConstants;
use Drupal\lnaddress\LnAddressServiceInterface;
use Drupal\lnaddress\LnAddressServiceTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the LnAddressAdminForm class.
 */
class LnAddressAdminForm extends ConfigFormBase {

  use LnAddressServiceTrait;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    LnAddressServiceInterface $lnaddress
  ) {
    parent::__construct($config_factory);

    $this->lnaddress = $lnaddress;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('lnaddress')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lnaddress_admin';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [LnAddressConstants::SETTINGS];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $key = LnAddressConstants::KEY_DOMAIN;

    $default_value = $this->lnaddress()->getDomain();

    $form[$key] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain'),
      '#required' => TRUE,
      '#default_value' => $default_value,
    ];

    $key = LnAddressConstants::KEY_ENABLED;

    $default_value = $this->lnaddress()->getEnabled();

    $form[$key] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable lightning address payments'),
      '#default_value' => $default_value,
    ];

    $key = LnAddressConstants::KEY_MIN_SENDABLE;

    $default_value = $this->lnaddress()->getMinSendable();
    $min = 0;
    $max = $this->lnaddress()->getMaxSendable();

    $form[$key] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum Sendable Amount'),
      '#default_value' => $default_value,
    ];

    if ($min != 0) {
      $form[$key]['#min'] = $min;
    }

    $key = LnAddressConstants::KEY_MAX_SENDABLE;

    $default_value = $this->lnaddress()->getMaxSendable();
    $min = $this->lnaddress()->getMinSendable();

    $form[$key] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Sendable Amount'),
      '#default_value' => $default_value,
    ];

    if ($min != 0) {
      $form[$key]['#min'] = $min;
    }

    if ($max != 0) {
      $form[$key]['#max'] = $max;
    }

    $key = LnAddressConstants::KEY_MAX_COMMENT_LENGTH;

    $default_value = $this->lnaddress()->getMaxCommentLength();

    $form[$key] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Comment Length'),
      '#default_value' => $default_value,
    ];

    $key = LnAddressConstants::KEY_LOGGING;

    $default_value = $this->lnaddress()->getLogging();

    $form[$key] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable logging'),
      '#default_value' => $default_value,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->cleanValues()->getValues();

    $this->lnaddress()->saveConfiguration($values);

    parent::submitForm($form, $form_state);
  }

}
