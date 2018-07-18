<?php 
namespace Creditkey\B2BGateway\Controller; 

use Magento\Checkout\Controller\Express\RedirectLoginInterface;
use Magento\Framework\App\Action\Action as AppAction; 

abstract class AbstractCreditkey extends AppAction implements RedirectLoginInterface {
    protected $_checkout;
    protected $_checkoutTypes = [];
    protected $_config;
    protected $_quote;
    protected $_configType;
    protected $_configMethod;
    protected $_checkoutType;
    protected $_customerSession;
    protected $_checkoutSession;
    protected $_orderFactory;
    protected $_checkoutFactory;
    protected $_urlHelper;
    protected $_customerUrl;
    protected $_configScopeConfigInterface;
    protected $_modelOrder;
    protected $_modelCart;
    protected $_cartManagement;
    protected $_quoteManagement;
    protected $_storeManager;
    protected $_customerFactory;
    protected $_customerRepo;
    protected $_logger;

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
      $this->_customerSession = $customerSession;
      $this->_checkoutSession = $checkoutSession;
      $this->_orderFactory = $orderFactory;
      $this->_checkoutFactory = $checkoutFactory;
      $this->_urlHelper = $urlHelper;
      $this->_customerUrl = $customerUrl;
      $this->_configScopeConfigInterface = $scopeConfigInterface;
      $this->_modelOrder = $modelOrder;
      $this->_modelCart = $modelCart;
      $this->_cartManagement = $cartManagement;
      $this->_quoteManagement = $quoteManagement;
      $this->_storeManager = $storeManager;
      $this->_customerFactory = $customerFactory;
      $this->_customerRepo = $customerRepository;
      $this->_logger = $logger;
      parent::__construct($context);
    }

    protected function _getCheckoutSession() {
      return $this->_checkoutSession;
    }

    protected function _getQuote() {
      if (!$this->_quote) {
        $this->_quote = $this->_getCheckoutSession()->getQuote();
      }
      return $this->_quote;
    }

    public function getCustomerBeforeAuthUrl() {
        return;
    }
    /**
     * Returns a list of action flags [flag_key] => boolean
     * @return array
     */
    public function getActionFlagList() {
        return [];
    }

    /**
     * Returns login url parameter for redirect
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_customerUrl->getLoginUrl();
    }
    /**
     * Returns action name which requires redirect
     * @return string
     */
    public function getRedirectActionName()
    {
        return 'start';
    }
  }
