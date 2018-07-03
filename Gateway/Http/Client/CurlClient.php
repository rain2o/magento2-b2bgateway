<?php
namespace Creditkey\B2BGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Payment\Model\Method\Logger;

class CurlClient implements ClientInterface
{
    private $logger;
    private $scopeConfig;

    public function __construct(
      ScopeConfigInterface $scopeConfig,
      Logger $logger
    ) {
      $this->scopeConfig = $scopeConfig;
      $this->logger = $logger;
    }

    public function placeRequest(TransferInterface $transferObject) {
      try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $transferObject->getBody());
        curl_setopt($curl, CURLOPT_URL, $transferObject->getUri());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ((bool)$this->scopeConfig->getValue('payment/creditkey_gateway/creditkey_debug', ScopeInterface::SCOPE_STORE)) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        $result = curl_exec($curl);
      } catch (Exception $e) {
        $this->logger->debug('curl error: '.curl_errno($curl).' - '.curl_error($curl));
        throw $e;
      }
      
      return [ json_decode($result) ];
    }
}
