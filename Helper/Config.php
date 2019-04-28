<?php
namespace CreditKey\B2BGateway\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Config Helper
 */
class Config
{
    /**
     * Config paths
     */
    const XML_PATH_PAYMENT_CKGATEWAY     = 'payment/creditkey_gateway';
    const XML_KEY_ENDPOINT               = '/creditkey_endpoint';
    const XML_KEY_PUBLICKEY              = '/creditkey_publickey';
    const XML_KEY_SECRET                 = '/creditkey_sharedsecret';
    const XML_KEY_PDP_MARKETING_ACTIVE   = '/creditkey_productmarketing/active';
    const XML_KEY_PDP_MARKETING_PRODUCTS = '/creditkey_productmarketing/products';
    const XML_KEY_PDP_MARKETING_TYPE     = '/creditkey_productmarketing/type';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get config value at the specified key
     *
     * @param string $key
     * @return mixed
     */
    private function getConfigValue($key)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_CKGATEWAY . $key,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get API Endpoing
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getConfigValue(self::XML_KEY_ENDPOINT);
    }

    /**
     * Get Public Key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->getConfigValue(self::XML_KEY_PUBLICKEY);
    }

    /**
     * Get Shared Secret
     *
     * @return string
     */
    public function getSharedSecret()
    {
        return $this->getConfigValue(self::XML_KEY_SECRET);
    }

    /**
     * Is displaying marketing content on product
     * details page enabled
     *
     * @return boolean
     */
    public function isPdpMarketingActive()
    {
        return (boolean) $this->getConfigValue(self::XML_KEY_PDP_MARKETING_ACTIVE);
    }

    /**
     * Get array of product IDs selected to display marketing content
     *
     * @return array
     */
    public function getPdpMarketingProducts()
    {
        $productIds = $this->getConfigValue(self::XML_KEY_PDP_MARKETING_PRODUCTS);
        return explode(',', $productIds);
    }

    /**
     * Get type of marketing content to display
     * on product details page
     *
     * @return string
     */
    public function getPdpMarketingType()
    {
        return $this->getConfigValue(self::XML_KEY_PDP_MARKETING_TYPE);
    }
}
