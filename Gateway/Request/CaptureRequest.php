<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Creditkey\B2BGateway\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use \Psr\Log\LoggerInterface;

class CaptureRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $scopeConfig;
    private $logger;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
      ScopeConfigInterface $scopeConfig,
      LoggerInterface $logger
    ) {
      $this->scopeConfig = $scopeConfig;
      $this->logger = $logger;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();

        $ckKey = $payment->getAdditionalInformation('ck_public_key');

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }

        $postData = array(
          'action' => 'capture',
          'ck_key' => $ckKey,
          'baseUrl' => $this->scopeConfig->getValue('payment/creditkey_gateway/creditkey_endpoint', ScopeInterface::SCOPE_STORE),
          'public_key' => $this->scopeConfig->getValue('payment/creditkey_gateway/creditkey_publickey', ScopeInterface::SCOPE_STORE),
          'shared_secret' => $this->scopeConfig->getValue('payment/creditkey_gateway/creditkey_sharedsecret', ScopeInterface::SCOPE_STORE),
          'order[amount]' => $payment->getAmountOrdered()
        );

        return $postData;
    }
}
