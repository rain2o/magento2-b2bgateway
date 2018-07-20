<?php
namespace Creditkey\B2BGateway\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use \Psr\Log\LoggerInterface;

class CheckoutObserver implements ObserverInterface {
  protected $logger;
  protected $scopeConfig;
  protected $checkoutSession;
  protected $customerSession;

  public function __construct(
    LoggerInterface $logger,
    ScopeConfigInterface $scopeConfig,
    CheckoutSession $checkoutSession,
    CustomerSession $customerSession
  ) {
    $this->logger = $logger;
    $this->scopeConfig = $scopeConfig;
    $this->checkoutSession = $checkoutSession;
    $this->customerSession = $customerSession;
  }

  public function execute(Observer $observer) {
    $quote = $this->checkoutSession->getQuote();
    $customer = $this->customerSession->getCustomer();

    try {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
        'app_id' => '951627502',
        'identity' => $customer->getEmail(),
        'identity_type' => 'email',
        'event' => 'Customer Viewed Checkout',
        'timestamp' => date('c'),
        'properties' => [
          'store' => $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
          'grand_total' => $quote->getGrandTotal()
        ]
      ]));
      curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($curl, CURLOPT_HEADER, 1);
      curl_setopt($curl, CURLOPT_URL, 'https://heapanalytics.com/api/track');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($curl);
    } catch (Exception $e) {
      $this->logger->debug('curl error: '.curl_errno($curl).' - '.curl_error($curl));
      throw $e;
    }
  }
}
