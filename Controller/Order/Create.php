<?php
    namespace CreditKey\B2BGateway\Controller\Order;

    use Magento\Framework\Controller\ResultFactory;

    class Create extends \CreditKey\B2BGateway\Controller\AbstractCreditKeyController
    {
        protected $_urlBuilder;
        protected $_resultFactory;

        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
            \CreditKey\B2BGateway\Helper\Data $creditKeyData,
            \Magento\Customer\Model\Url $customerUrl,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Customer\Model\Session $customerSession,
            \Psr\Log\LoggerInterface $logger,
            \Magento\Framework\UrlInterface $urlBuilder,
            \Magento\Framework\Controller\ResultFactory $resultFactory
        ) {
            $this->_urlBuilder = $urlBuilder;
            $this->_resultFactory = $resultFactory;

            parent::__construct(
                $context,
                $creditKeyApi,
                $creditKeyData,
                $customerUrl,
                $checkoutSession,
                $customerSession,
                $logger
            );
        }

        public function execute() {
            $quote = $this->_checkoutSession->getQuote();

            $cartContents = $this->_creditKeyData->buildCartContents($quote);
            $billingAddress = $this->_creditKeyData->buildAddress($quote->getBillingAddress());
            $shippingAddress = $this->_creditKeyData->buildAddress($quote->getShippingAddress());
            $charges = $this->_creditKeyData->buildCharges($quote);

            $remoteId = $quote->getId();
            $customerId = null;
            if ($this->_customerSession->isLoggedIn())
                $customerId = $this->_customerSession->getCustomer()->getId();

            $returnUrl = $this->_urlBuilder->getUrl('creditkey_gateway/order/complete', [
              'ref' => $remoteId,
              'key' => '%CKKEY%',
              '_secure' => true
            ]);
            $cancelUrl = $this->_urlBuilder->getUrl('creditkey_gateway/order/cancel');

            $this->_creditKeyApi->configure();
            $redirectTo = \CreditKey\Checkout::beginCheckout($cartContents, $billingAddress, $shippingAddress,
                $charges, $remoteId, $customerId, $returnUrl, $cancelUrl);

            $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($redirectTo);
            return $resultRedirect;
        }
    }
