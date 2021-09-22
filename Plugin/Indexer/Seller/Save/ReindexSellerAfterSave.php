<?php
namespace Lof\ElasticsuiteSeller\Plugin\Indexer\Seller\Save;

use Magento\Framework\Indexer\IndexerRegistry;
use Lof\ElasticsuiteSeller\Model\Seller\Indexer\Fulltext;
use Lof\ElasticsuiteSeller\Helper\Configuration;
use Lof\MarketPlace\Model\Seller;


class ReindexSellerAfterSave
{
    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * ReindexCategoryAfterSave constructor.
     *
     * @param IndexerRegistry $indexerRegistry The indexer registry
     * @param Configuration $configuration module configuration helper
     */
    public function __construct(IndexerRegistry $indexerRegistry, Configuration $configuration)
    {
        $this->indexerRegistry = $indexerRegistry;
        $this->configuration = $configuration;
    }

    /**
     * Reindex marketplace sellers data into search engine after saving the marketplace seller
     *
     * @param Seller $subject The marketplace seller being reindexed
     * @param Seller $result  The parent function we are plugged on
     *
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function afterSave(
        Seller $subject,
        $result
    ) {
        if ($subject->getIsSearchable() && $this->configuration->isEnabled()) {
            $marketplaceSellerIndexer = $this->indexerRegistry->get(Fulltext::INDEXER_ID);
            $marketplaceSellerIndexer->reindexRow($subject->getId());
        }

        return $result;
    }
}
