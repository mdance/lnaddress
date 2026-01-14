<?php

namespace Drupal\lnaddress;

/**
 * Provides the LnAddressServiceTrait trait.
 */
trait LnAddressServiceTrait {

  /**
   * Provides the lightning network authentication service.
   */
  protected ?LnAddressServiceInterface $lnaddress = NULL;

  /**
   * Provides the lightning network authentication service.
   *
   * @return \Drupal\lnaddress\LnAddressServiceInterface
   */
  public function lnaddress(): LnAddressServiceInterface {
    if (is_null($this->lnaddress)) {
      $this->lnaddress = \Drupal::service('lnaddress');
    }

    return $this->lnaddress;
  }

}
