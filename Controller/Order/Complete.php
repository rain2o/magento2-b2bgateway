<?php
    namespace CreditKey\B2BGateway\Controller\Order;

    use Magento\Framework\Controller\ResultFactory;

    class Complete extends \CreditKey\B2BGateway\Controller\AbstractCreditKeyController
    {
        protected $_quoteManagement;
        protected $_modelCart;

        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
            \CreditKey\B2BGateway\Helper\Data $creditKeyData,
            \Magento\Customer\Model\Url $customerUrl,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Customer\Model\Session $customerSession,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Psr\Log\LoggerInterface $logger,
            \Magento\Framework\Controller\ResultFactory $resultFactory,
            \Magento\Quote\Model\QuoteManagement $quoteManagement,
            \Magento\Checkout\Model\Cart $modelCart
        ) {
            $this->_quoteManagement = $quoteManagement;
            $this->_resultFactory = $resultFactory;
            $this->_modelCart = $modelCart;

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

      public function execute()
      {
          $quoteId = $this->getRequest()->getParam('ref');
          $ckOrderId = $this->getRequest()->getParam('key');

          $quote = $this->_checkoutSession->getQuote();

          if ($quote->getId() != $quoteId)
          {
              // Checkout session expired - redirect back to checkout
              $this->_redirect('checkout');
              return $this;
          }

          // Check that the payment is authorized
          $this->_creditKeyApi->configure();
          $isAuthorized = false;
          try
          {
              $isAuthorized = \CreditKey\Checkout::completeCheckout($ckOrderId);
          }
          catch (\Exception $e)
          {
                $this->_logger->critical($e);
                $this->_messageManager->addErrorMessage(__('CREDIT_KEY_AUTH_FAILED'));
                $this->_redirect('checkout');
                return $this;
          }

          if (!$isAuthorized)
          {
              // Payment not authorized - redirect back to checkout
              $this->_redirect('checkout');
              return $this;
          }

          $this->_checkoutSession
              ->setLastQuoteId($quote->getId())
              ->setLastSuccessQuoteId($quote->getId())
              ->clearHelperData();

          $email = $quote->getBillingAddress()->getEmail();

          if (!$this->_customerSession->isLoggedIn()) {
              $quote->setCheckoutMethod('guest');
              $quote->setCustomerIsGuest(true);
              $quote->setCustomerEmail($email);
          }

          $order = $this->_quoteManagement->submit($quote);

          if ($order) {
              $this->_checkoutSession
                  ->setLastOrderId($order->getId())
                  ->setLastRealOrderId($order->getIncrementId())
                  ->setLastOrderStatus($order->getStatus());

              $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
              $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
              $order->save();

              $orderPayment = $order->getPayment();
              $orderPayment->setAdditionalInformation('ckOrderId', $ckOrderId);
              $orderPayment->setTransactionId($ckOrderId);
              $orderPayment->setState('paid');
              $order->save();

              try
              {
                  // Send the Magento Order ID and Status to Credit Key
                  \CreditKey\Orders::update($ckOrderId, $order->getState(), $order->getIncrementId(), null, null, null);
              }
              catch (\Exception $e)
              {
                  $this->_logger->critical($e);
              }
          }

          $cart = $this->_modelCart;
          $cart->truncate();
          $cart->save();
          $items = $quote->getAllVisibleItems();
          foreach($items as $item) {
              $itemId = $item->getItemId();
              $cart->removeItem($itemId)->save();
          }

          $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
          $resultRedirect->setPath('checkout/onepage/success');
          return $resultRedirect;
      }
  }
