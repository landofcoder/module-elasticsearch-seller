<?php

namespace Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Indexer\Fulltext\Action;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticsuiteCore\Model\ResourceModel\Indexer\AbstractIndexer;

class Full extends AbstractIndexer
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * Full constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection     $resource     Resource Connection
     * @param \Magento\Store\Model\StoreManagerInterface    $storeManager Store Manager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool Metadata Pool
     */
    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($resource, $storeManager);
    }

    /**
     * Load a bulk of marketplace seller data.
     *
     * @param int     $storeId    Store id.
     * @param string  $sellerIds seller ids filter.
     * @param integer $fromId     Load product with id greater than.
     * @param integer $limit      Number of product to get loaded.
     *
     * @return array
     */
    public function getSearchableSeller($storeId, $sellerIds = null, $fromId = 0, $limit = 100)
    {
        $select = $this->getConnection()->select()
                       ->from(['p' => $this->getTable('lof_marketplace_seller')]);

        $this->addIsVisibleInStoreFilter($select, $storeId);

        if ($sellerIds !== null) {
            $select->where('p.seller_id IN (?)', $sellerIds);
        }

        $select->where('p.seller_id > ?', $fromId)
               ->where('p.is_searchable = ?', true)
               ->limit($limit)
               ->order('p.seller_id');

        return $this->connection->fetchAll($select);
    }

    /**
     * Filter the select to append only marketplace seller of current store.
     *
     * @param \Zend_Db_Select $select  Product select to be filtered.
     * @param integer         $storeId Store Id
     *
     * @return \Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Indexer\Fulltext\Action\Full Self Reference
     */
    private function addIsVisibleInStoreFilter($select, $storeId)
    {
        $select->join(
            ['ps' => $this->getTable('lof_marketplace_store')],
            "p.seller_id = ps.seller_id"
        );
        $select->where('ps.store_id IN (?)', [0, $storeId]);

        return $this;
    }
}
