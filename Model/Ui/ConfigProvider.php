<?php
namespace CreditKey\B2BGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\View\Asset\Repository;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'creditkey_gateway';

    protected $cart;
    protected $urlBuilder;
    protected $_configScopeConfigInterface;
    protected $_assetRepo;

    public function __construct(
      Cart $cart,
      UrlInterface $urlBuilder,
      ScopeConfigInterface $configScopeConfigInterface,
      Repository $_assetRepo
    ) {
      $this->_cart = $cart;
      $this->_urlBuilder = $urlBuilder;
      $this->_configScopeConfigInterface = $configScopeConfigInterface;
      $this->_assetRepo = $_assetRepo;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'assetSrc' => $this->_assetRepo->getUrl("CreditKey_B2BGateway::images/ck-logo-new.svg"),
                    'quoteId' => $this->_cart->getQuote()->getId(),
                    'baseUrl' => $this->_urlBuilder->getUrl(),
                    'redirectUrl' => $this->_urlBuilder->getUrl('creditkey_gateway/order/create')
                ]
            ]
        ];
    }
}
