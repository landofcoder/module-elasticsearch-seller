<?php
namespace Lof\ElasticsuiteSeller\Model\Seller\Indexer\Fulltext\Action;

use Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Indexer\Fulltext\Action\Full as ResourceModel;
use Magento\Cms\Model\Template\FilterProvider;

class Full
{
    /**
     * @var \Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Indexer\Fulltext\Action\Full
     */
    private $resourceModel;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * Constructor.
     *
     * @param ResourceModel  $resourceModel  Indexer resource model.
     * @param FilterProvider $filterProvider Model template filter provider.
     */
    public function __construct(ResourceModel $resourceModel, FilterProvider $filterProvider)
    {
        $this->resourceModel  = $resourceModel;
        $this->filterProvider = $filterProvider;
    }

    /**
     * Get data for a list of marketplace sellers in a store id.
     *
     * @param integer    $storeId    Store id.
     * @param array|null $sellerIds List of seller ids.
     *
     * @return \Traversable
     */
    public function rebuildStoreIndex($storeId, $sellerIds = null)
    {
        $lastSellerId  = 0;

        do {
            $sellers = $this->getSearchableSeller($storeId, $sellerIds, $lastSellerId);
            foreach ($sellers as $sellerData) {
                $sellerData = $this->processSellerData($sellerData);
                $lastSellerId = (int) $sellerData['seller_id'];
                yield $lastSellerId => $sellerData;
            }
        } while (!empty($sellers));
    }

    /**
     * Load a bulk of marketplace seller data.
     *
     * @param int     $storeId    Store id.
     * @param string  $sellerIds  Seller ids filter.
     * @param integer $fromId     Load product with id greater than.
     * @param integer $limit      Number of product to get loaded.
     *
     * @return array
     */
    private function getSearchableSeller($storeId, $sellerIds = null, $fromId = 0, $limit = 100)
    {
        return $this->resourceModel->getSearchableSeller($storeId, $sellerIds, $fromId, $limit);
    }

    /**
     * Parse template processor seller description
     *
     * @param array $sellerData sellerdata.
     *
     * @return array
     */
    private function processSellerData($sellerData)
    {
        if (isset($sellerData['description'])) {
            $sellerData['description'] = $this->filterProvider->getPageFilter()->filter($sellerData['description']);
        }

        return $sellerData;
    }
}
