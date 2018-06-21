<?php
namespace CreditKey\B2BGateway\Model;

class Options {
  public function toOptionArray()
  {
    return [
      ['value'=>1, 'label'=>'API Endpoint'],
      ['value'=>2, 'label'=>'Public Key'],
      ['value'=>3, 'label'=>'Shared Secret']
    ];
  }
}
?>

