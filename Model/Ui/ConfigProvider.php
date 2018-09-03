<?php
namespace CreditKey\B2BGateway\Model\Ui;

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
    protected $_creditKeyApi;
    protected $_creditKeyData;
    protected $_logger;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\UrlInterface $urlBuilder,
        \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
        \CreditKey\B2BGateway\Helper\Data $creditKeyData,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_cart = $cart;
        $this->_assetRepo = $assetRepo;
        $this->_customerSession = $customerSession;
        $this->_urlBuilder = $urlBuilder;
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
                    'assetSrc' => $this->_assetRepo->getUrl("CreditKey_B2BGateway::images/ck-logo-new.svg"),
                    'redirectUrl' => $this->_urlBuilder->getUrl('creditkey_gateway/order/create'),
                    'isCreditKeyDisplayed' => $isCreditKeyDisplayed
                ]
            ]
        ];
    }
}
