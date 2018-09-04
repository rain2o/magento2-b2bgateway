<?php
  namespace CreditKey\B2BGateway\Controller\Order;

  class Cancel extends \CreditKey\B2BGateway\Controller\AbstractCreditKeyController {
      public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
            \CreditKey\B2BGateway\Helper\Data $creditKeyData,
            \Magento\Customer\Model\Url $customerUrl,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Customer\Model\Session $customerSession,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Psr\Log\LoggerInterface $logger
      ) {
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
          $this->_redirect('checkout');
          return $this;
      }
  }
