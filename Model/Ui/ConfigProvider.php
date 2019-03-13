<?php
namespace CreditKey\B2BGateway\Model\Ui;

use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const CODE = 'creditkey_gateway';

    protected $_cart;
    protected $_assetRepo;
    protected $_customerSession;
    protected $_urlBuilder;
    protected $_configScopeConfigInterface;
    protected $_creditKeyApi;
    protected $_creditKeyData;
    protected $_logger;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
        \CreditKey\B2BGateway\Helper\Data $creditKeyData,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_cart = $cart;
        $this->_assetRepo = $assetRepo;
        $this->_customerSession = $customerSession;
        $this->_urlBuilder = $urlBuilder;
        $this->_configScopeConfigInterface = $scopeConfigInterface;
        $this->_creditKeyApi = $creditKeyApi;
        $this->_creditKeyData = $creditKeyData;
        $this->_logger = $logger;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $quote = $this->_cart->getQuote();
        $cartContents = $this->_creditKeyData->buildCartContents($quote);
        $customerId = null;
        if ($this->_customerSession->isLoggedIn())
            $customerId = $this->_customerSession->getCustomer()->getId();

        $this->_creditKeyApi->configure();
        $isCreditKeyDisplayed = false;
        try
        {
            $isCreditKeyDisplayed = \CreditKey\Checkout::isDisplayedInCheckout($cartContents, $customerId);
        }
        catch (\Exception $e)
        {
            $this->_logger->critical($e);
            /* swallow any exception, and don't display the CK option */
        }

        return [
            'payment' => [
                self::CODE => [
                    'endpoint' => $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_endpoint', ScopeInterface::SCOPE_STORE),
                    'assetSrc' => $this->_assetRepo->getUrl("CreditKey_B2BGateway::images/ck-logo-new.svg"),
                    'redirectUrl' => $this->_urlBuilder->getUrl('creditkey_gateway/order/create'),
                    'publicKey' => $this->_creditKeyApi->public_key(),
                    'isCreditKeyDisplayed' => $isCreditKeyDisplayed
                ]
            ]
        ];
    }
}
