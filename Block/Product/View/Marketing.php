<?php
namespace CreditKey\B2BGateway\Block\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Store\Model\ScopeInterface;

/**
 * Marketing Block
 */
class Marketing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    private $product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $json;

    /**
     * @var \CreditKey\B2BGateway\Helper\Api
     */
    private $creditKeyApi;

    /**
     * @var \Magento\Tax\Api\TaxCalculationInterface
     */
    private $taxCalculation;

    /**
     * Stored array of product ids authorized to display marketing content
     *
     * @var array
     */
    private $authorizedProducts;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\SerializerInterface $json
     * @param \CreditKey\B2BGateway\Helper\Api $creditKeyApi
     * @param \Magento\Tax\Api\TaxCalculationInterface $taxCalculation
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $json,
        \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculation,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
        $this->creditKeyApi = $creditKeyApi;
        $this->taxCalculation = $taxCalculation;
        parent::__construct($context, $data);
    }

    /**
     * Returns a Product
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }

    /**
     * Check if the current product is authorized to display marketing content
     *
     * @return bool
     */
    public function isAuthorized()
    {
        $product = $this->getProduct();

        return (bool) ($product
                && $product->getId()
                && in_array($product->getId(), $this->getAuthorizedProducts())
        );
    }

    /**
     * Get JSON config for JS component
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $this->creditKeyApi->configure();

        $config = [
            'ckConfig' => [
                'endpoint' => $this->scopeConfig->getValue(
                    'payment/creditkey_gateway/creditkey_endpoint',
                    ScopeInterface::SCOPE_STORE
                ),
                'publicKey' => $this->creditKeyApi->public_key(),
                'charges' => $this->getCharges()
            ]
        ];

        return $this->json->serialize($config);
    }

    /**
     * Get an array of the charges for the product
     *
     * @return array of charges as follows
     * [total, shipping, tax, discount_amount, grand_total]
     */
    private function getCharges()
    {
        $product = $this->getProduct();

        $taxClassId = $product->getCustomAttribute('tax_class_id');
        $taxRate = $taxClassId
            ? $this->taxCalculation->getCalculatedRate($taxClassId->getValue())
            : 0;

        return [
            $product->getFinalPrice(),
            0, // no quote yet to calc shipping
            $taxRate,
            0, // no quote to apply discount
            $product->getFinalPrice() + $taxRate
        ];
    }

    /**
     * Return an array of products authorized to display
     * our marketing content
     *
     * @return array
     */
    private function getAuthorizedProducts()
    {
        if (!$this->authorizedProducts) {
            $productIds = $this->scopeConfig->getValue(
                'payment/creditkey_gateway/creditkey_productmarketing/products',
                ScopeInterface::SCOPE_STORE
            );
            $this->authorizedProducts = explode(",", $productIds);
        }
        return $this->authorizedProducts;
    }
}
