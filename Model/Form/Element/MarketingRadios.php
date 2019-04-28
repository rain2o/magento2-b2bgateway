<?php
namespace CreditKey\B2BGateway\Model\Form\Element;

use Magento\Framework\Escaper;
use Magento\Store\Model\ScopeInterface;

/**
 * MarketingRadios Form Element
 */
class MarketingRadios extends \Magento\Framework\Data\Form\Element\Radios
{
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
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\SerializerInterface $json
     * @param \CreditKey\B2BGateway\Helper\Api $creditKeyApi
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $json,
        \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
        $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
        $this->creditKeyApi = $creditKeyApi;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('radios');
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $value = $this->getValue();
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                $html .= $this->_optionToHtml($option, $value);
            }
        }
        $html .= $this->getAfterElementHtml();

        // initialize our js component
        $id = $this->getHtmlId();
        $html .= "
                <script type='text/x-magento-init'>
                    {
                        \"input[id^={$id}]\": {
                            \"CreditKey_B2BGateway/js/config/creditkey_marketing\": " . $this->getJsonConfig() . "
                        }
                    }
                </script>";

        return $html;
    }

    /**
     * Get JSON config for JS component
     *
     * @return string
     */
    private function getJsonConfig()
    {
        $this->creditKeyApi->configure();

        $config = [
            'ckConfig' => [
                'endpoint' => $this->scopeConfig->getValue(
                    'payment/creditkey_gateway/creditkey_endpoint',
                    ScopeInterface::SCOPE_STORE
                ),
                'publicKey' => $this->creditKeyApi->public_key()
            ]
        ];

        return $this->json->serialize($config);
    }
}
