<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Creditkey\B2BGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Cart;
use Creditkey\B2BGateway\Gateway\Http\Client\ClientMock;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'creditkey_gateway';

    protected $cart;
    protected $urlBuilder;
    protected $_configScopeConfigInterface;

    public function __construct(
      Cart $cart,
      UrlInterface $urlBuilder,
      ScopeConfigInterface $configScopeConfigInterface
    ) {
      $this->_cart = $cart;
      $this->_urlBuilder = $urlBuilder;
      $this->_configScopeConfigInterface = $configScopeConfigInterface;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'quoteId' => $this->_cart->getQuote()->getId(),
                    'redirectUrl' => $this->getMerchantEndpoint(),
                    'publicKey' => $this->getPublicKey(),
                    'baseUrl' => $this->_urlBuilder->getUrl(),
                    'returnUrl' => $this->_urlBuilder->getUrl('creditkey_gateway/order/complete'),
                    'cancelUrl'=> $this->_urlBuilder->getUrl('creditkey_gateway/order/cancel'),
                    'transactionResults' => [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ]
                ]
            ]
        ];
    }

    public function getMerchantEndpoint() {
      return $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_endpoint', ScopeInterface::SCOPE_STORE) . '/api/credit_apps/fullpage_checkout?';
    }

    public function getPublicKey() {
      return $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_publickey', ScopeInterface::SCOPE_STORE);
    }
}
