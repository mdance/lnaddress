<?php

namespace Drupal\lnaddress\Form;

use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\lnaddress\LnAddressConstants;
use Drupal\lnaddress\LnAddressServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the LnAddressAdminForm class.
 */
class LnAddressAdminForm extends ConfigFormBase {

  /**
   * Provides the constructor method.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typedConfigManager
   *   The typed config manager.
   * @param \Drupal\lnaddress\LnAddressServiceInterface $service
   *   Provides the module service.
   */
  public function __construct(
    protected $configFactory,
    protected TypedConfigManagerInterface $typedConfigManager,
    protected LnAddressServiceInterface $service
  ) {
    parent::__construct($configFactory, $typedConfigManager);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'lnaddress_admin';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [LnAddressConstants::SETTINGS];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $key = LnAddressConstants::KEY_DOMAIN;

    $default_value = $this->service->getDomain();

    $form[$key] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain'),
      '#required' => TRUE,
      '#default_value' => $default_value,
    ];

    $key = LnAddressConstants::KEY_ENABLED;

    $default_value = $this->service->getEnabled();

    $form[$key] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable lightning address payments'),
      '#default_value' => $default_value,
    ];

    $key = LnAddressConstants::KEY_MIN_SENDABLE;

    $default_value = $this->service->getMinSendable();
    $min = 0;
    $max = $this->service->getMaxSendable();

    $form[$key] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum Sendable Amount'),
      '#default_value' => $default_value,
    ];

    if ($min != 0) {
      $form[$key]['#min'] = $min;
    }

    $key = LnAddressConstants::KEY_MAX_SENDABLE;

    $default_value = $this->service->getMaxSendable();
    $min = $this->service->getMinSendable();

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

    $default_value = $this->service->getMaxCommentLength();

    $form[$key] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Comment Length'),
      '#default_value' => $default_value,
    ];

    $key = LnAddressConstants::KEY_LOGGING;

    $default_value = $this->service->getLogging();

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

    $this->service->saveConfiguration($values);

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('lnaddress'),
    );
  }

}
