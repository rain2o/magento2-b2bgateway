<?php
namespace CreditKey\B2BGateway\Model\Adminhtml\Source;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Products
 */
class Products implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * String pattern for multiselect label
     *
     * @var string
     */
    private $labelPattern = "%s [%s]";

    /**
     * Construct
     *
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param string $labelPattern
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Psr\Log\LoggerInterface $logger,
        string $labelPattern = null
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;

        // Allows for extendable formatting of multiselect label
        if ($labelPattern !== null) {
            $this->labelPattern = $labelPattern;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [];
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create());
        $total = $products->getTotalCount();

        if ($total > 1000) {
          $options[] = [
            'value' => 0,
            'label' => 'Unable to list products (Too many listings)'
          ];

          $options[] = [
            'value' => 0,
            'label' => 'Will default to all products enabled'
          ];

          return $options;
        } else {
          $allProducts = $products->getItems();
        }

        foreach ($allProducts as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => sprintf(
                    $this->labelPattern,
                    $product->getName(),
                    $product->getSku()
                )
            ];
        }

        return $options;
    }
}
