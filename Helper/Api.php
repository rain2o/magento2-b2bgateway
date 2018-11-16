<?php
    namespace CreditKey\B2BGateway\Helper;

    use Magento\Store\Model\ScopeInterface;
    
    class Api
    {
        protected $_configScopeConfigInterface;

        public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
        ) {
            $this->_configScopeConfigInterface = $scopeConfigInterface;
        }

        public function configure()
        {
            $endpoint = $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_endpoint', ScopeInterface::SCOPE_STORE);
            $publicKey = $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_publickey', ScopeInterface::SCOPE_STORE);
            $sharedSecret = $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_sharedsecret', ScopeInterface::SCOPE_STORE);
            \CreditKey\Api::configure($endpoint, $publicKey, $sharedSecret);
        }

        public function public_key() 
        {
          return $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_publickey', ScopeInterface::SCOPE_STORE);
        }
    }
