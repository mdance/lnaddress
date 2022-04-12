<?php

namespace Drupal\lnaddress\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\lnaddress\LnAddressConstants;
use Drupal\lnaddress\LnAddressServiceInterface;
use Drupal\lnaddress\LnAddressServiceTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the UserDefaultsForm class.
 */
class UserDefaultsForm extends FormBase {

  use LnAddressServiceTrait;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    LnAddressServiceInterface $lnaddress
  ) {
    $this->lnaddress = $lnaddress;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('lnaddress')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lnaddress_user_defaults_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $user = NULL) {
    $uid = NULL;

    if ($user) {
      $uid = $user->id();
    }

    $user_data = $this->lnaddress()->getUserData($uid);

    $key = 'enabled';

    $default_value = $user_data['enabled'];

    $form[$key] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable lightning address payments'),
      '#default_value' => $default_value,
    ];

    $key = 'min_sendable';

    $default_value = $user_data['min_sendable'];
    $min = $user_data['min_sendable'];
    $max = $user_data['max_sendable'];

    $form[$key] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum Sendable Amount'),
      '#default_value' => $default_value,
      '#min' => $min,
      '#max' => $max,
    ];

    $key = 'max_sendable';

    $default_value = $user_data['max_sendable'];
    $min = $user_data['min_sendable'];
    $max = $user_data['max_sendable'];

    $form[$key] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Sendable Amount'),
      '#default_value' => $default_value,
      '#min' => $min,
      '#max' => $max,
    ];

    $form['actions'] = [
      '#type' => 'actions,'
    ];

    $actions = &$form['actions'];

    $actions['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->cleanValues()->getValues();
  }

}
