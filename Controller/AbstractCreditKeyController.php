<?php
    namespace CreditKey\B2BGateway\Controller; 

    use Magento\Checkout\Controller\Express\RedirectLoginInterface;
    use Magento\Framework\App\Action\Action as AppAction;
    use Magento\Store\Model\ScopeInterface;

    abstract class AbstractCreditKeyController extends AppAction implements RedirectLoginInterface
    {
        protected $_creditKeyApi;
        protected $_creditKeyData;
        protected $_customerUrl;
        protected $_checkoutSession;
        protected $_customerSession;
        protected $_logger;

        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
            \CreditKey\B2BGateway\Helper\Data $creditKeyData,
            \Magento\Customer\Model\Url $customerUrl,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Customer\Model\Session $customerSession,
            \Psr\Log\LoggerInterface $logger
        ) {
            $this->_creditKeyApi = $creditKeyApi;
            $this->_creditKeyData = $creditKeyData;
            $this->_customerUrl = $customerUrl;
            $this->_checkoutSession = $checkoutSession;
            $this->_customerSession = $customerSession;
            $this->_logger = $logger;
            parent::__construct($context);
        }

        public function getActionFlagList()
        {
            return [];
        }

        public function getCustomerBeforeAuthUrl()
        {
            return;
        }

        public function getLoginUrl()
        {
            return $this->_customerUrl->getLoginUrl();
        }

        public function getRedirectActionName()
        {
            return 'start';
        }
    }
