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
    const XML_PATH_PAYMENT_CKGATEWAY      = 'payment/creditkey_gateway';
    const XML_KEY_ENDPOINT                = '/creditkey_endpoint';
    const XML_KEY_PUBLICKEY               = '/creditkey_publickey';
    const XML_KEY_SECRET                  = '/creditkey_sharedsecret';
    const XML_KEY_CHECKOUT_MARKETING_TYPE = '/creditkey_checkoutdisplay';
    const XML_KEY_CHECKOUT_MARKETING_SIZE = '/creditkey_checkoutsize';
    const XML_KEY_PDP_MARKETING_ACTIVE    = '/creditkey_productmarketing/active';
    const XML_KEY_PDP_MARKETING_CATS      = '/creditkey_productmarketing/categories';
    const XML_KEY_PDP_MARKETING_PRICE     = '/creditkey_productmarketing/price';
    const XML_KEY_PDP_MARKETING_TYPE      = '/creditkey_productmarketing/type';
    const XML_KEY_PDP_MARKETING_SIZE      = '/creditkey_productmarketing/size';

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
     * Get the marketing display type for checkout
     *
     * @return string
     */
    public function getCheckoutMarketingType()
    {
        return $this->getConfigValue(self::XML_KEY_CHECKOUT_MARKETING_TYPE);
    }

    /**
     * Get the marketing display size for checkout
     *
     * @return string
     */
    public function getCheckoutMarketingSize()
    {
        return $this->getConfigValue(self::XML_KEY_CHECKOUT_MARKETING_SIZE);
    }

    /**
     * Get array of category IDs selected to display marketing content
     *
     * @return array
     */
    public function getPdpMarketingCategories()
    {
        $catIds = $this->getConfigValue(self::XML_KEY_PDP_MARKETING_CATS);
        return ($catIds === null) ? [] : explode(',', $catIds);
    }

    /**
     * Get price of marketing content to display
     * on product details page
     *
     * @return string
     */
    public function getPdpMarketingPrice()
    {
        return $this->getConfigValue(self::XML_KEY_PDP_MARKETING_PRICE);
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

    /**
     * Get size of marketing content to display
     * on product details page
     *
     * @return string
     */
    public function getPdpMarketingSize()
    {
        return $this->getConfigValue(self::XML_KEY_PDP_MARKETING_SIZE);
    }
}
