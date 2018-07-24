<?php
  namespace Creditkey\B2BGateway\Controller\Order;

  use Magento\Store\Model\ScopeInterface;

  class Cancel extends \Creditkey\B2BGateway\Controller\AbstractCreditkey {

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
      $this->_redirect('checkout');
      return $this;
    }
  }
