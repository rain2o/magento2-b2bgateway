<?php
    namespace CreditKey\B2BGateway\Controller\Order;

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
            \Psr\Log\LoggerInterface $logger,
            \Magento\Quote\Model\QuoteManagement $quoteManagement,
            \Magento\Checkout\Model\Cart $modelCart
        ) {
            $this->_quoteManagement = $quoteManagement;
            $this->_modelCart = $modelCart;

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
          $isAuthorized = \CreditKey\Checkout::completeCheckout($ckOrderId);

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

              // Send the Magento Order ID and Status to Credit Key
              \CreditKey\Orders::update($ckOrderId, $order->getState(), $order->getId(), null, null, null);
          }

          $cart = $this->_modelCart;
          $cart->truncate();
          $cart->save();
          $items = $quote->getAllVisibleItems();
          foreach($items as $item) {
              $itemId = $item->getItemId();
              $cart->removeItem($itemId)->save();
          }
          $this->_redirect('checkout/onepage/success');
          return $this;
      }
  }
