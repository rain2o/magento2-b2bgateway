<?php
  namespace Creditkey\B2BGateway\Controller\Order;

  use Magento\Store\Model\ScopeInterface;

  class Complete extends \Creditkey\B2BGateway\Controller\AbstractCreditkey {

    public function __construct(
      \Magento\Framework\App\Action\Context $context,
      \Magento\Customer\Model\Session $customerSession,
      \Magento\Checkout\Model\Session $checkoutSession,
      \Magento\Sales\Model\OrderFactory $orderFactory,
      \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory,
      \Magento\Framework\Url\Helper\Data $urlHelper,
      \Magento\Customer\Model\Url $customerUrl,
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
      \Magento\Sales\Model\Order $modelOrder,
      \Magento\Checkout\Model\Cart $modelCart,
      \Magento\Quote\Api\CartManagementInterface $cartManagement,
      \Magento\Quote\Model\QuoteManagement $quoteManagement,
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Customer\Model\CustomerFactory $customerFactory,
      \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
      \Psr\Log\LoggerInterface $logger
    ) {
      parent::__construct(
        $context,
        $customerSession,
        $checkoutSession,
        $orderFactory,
        $checkoutFactory,
        $urlHelper,
        $customerUrl,
        $scopeConfigInterface,
        $modelOrder,
        $modelCart,
        $cartManagement,
        $quoteManagement,
        $storeManager,
        $customerFactory,
        $customerRepository,
        $logger
      );
    }

    public function execute() {
      $ckKey = $this->getRequest()->getParam('key');

      $postData = [
          'public_key' => $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_publickey', ScopeInterface::SCOPE_STORE),
          'shared_secret' => $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_sharedsecret', ScopeInterface::SCOPE_STORE)
      ];

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
      curl_setopt($curl, CURLOPT_URL, $this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_endpoint', ScopeInterface::SCOPE_STORE)
          . '/api/credit_apps/place_order/' . $ckKey . '.json');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      if ((bool)$this->_configScopeConfigInterface->getValue('payment/creditkey_gateway/creditkey_debug', ScopeInterface::SCOPE_STORE)) {
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      }
      $result = curl_exec($curl);
      $response = json_decode($result);

      if (!is_object($response) || $response->status != "converted") {
          echo $result;
          return $this;
      }

      $quote = $this->_checkoutSession->getQuote();

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
        $orderPayment->setAdditionalInformation('ck_public_key', $ckKey);
        $orderPayment->setAdditionalInformation('ck_id', $response->id);
        $orderPayment->setState('paid');
        $order->save();
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
