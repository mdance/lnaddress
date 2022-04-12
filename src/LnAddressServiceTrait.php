<?php

namespace Drupal\lnaddress;

/**
 * Provides the LnAddressServiceTrait trait.
 */
trait LnAddressServiceTrait {

  /**
   * Provides the lightning network authentication service.
   *
   * @var \Drupal\lnaddress\LnAddressServiceInterface
   */
  protected $lnaddress;

  /**
   * Provides the lightning network authentication service.
   *
   * @return \Drupal\lnaddress\LnAddressServiceInterface
   */
  public function lnaddress() {
    if (is_null($this->lnaddress)) {
      $this->lnaddress = \Drupal::service('lnaddress');
    }

    return $this->lnaddress;
  }

}
