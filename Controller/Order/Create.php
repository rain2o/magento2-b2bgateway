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
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Psr\Log\LoggerInterface $logger,
            \Magento\Framework\UrlInterface $urlBuilder,
            \Magento\Framework\Controller\ResultFactory $resultFactory,
            \Magento\Framework\App\Request\Http $request
        ) {
            $this->_urlBuilder = $urlBuilder;
            $this->_resultFactory = $resultFactory;
            $this->request = $request;

            parent::__construct(
                $context,
                $creditKeyApi,
                $creditKeyData,
                $customerUrl,
                $checkoutSession,
                $customerSession,
                $messageManager,
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

            $mode = 'redirect';
            if ($this->request->getParam('modal')) {
              $mode = 'modal';
            }

            try
            {
                $redirectTo = \CreditKey\Checkout::beginCheckout($cartContents, $billingAddress, $shippingAddress,
                    $charges, $remoteId, $customerId, $returnUrl, $cancelUrl, $mode);

                $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($redirectTo);
                return $resultRedirect;
            }
            catch (\Exception $e)
            {
                $this->_logger->critical($e);
                $this->_messageManager->addErrorMessage(__('CREDIT_KEY_UNAVAILABLE'));
                $this->_redirect('checkout');
                return $this;
            }
        }
    }
