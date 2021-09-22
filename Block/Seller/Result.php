<?php


namespace Lof\ElasticsuiteSeller\Block\Seller;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Search\Model\QueryFactory;
use Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\CollectionFactory as SellerCollectionFactory;

class Result extends \Magento\Framework\View\Element\Template
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\Collection
     */
    private $sellerCollection;

    /**
     * Suggest constructor.
     *
     * @param TemplateContext          $context               Template contexte.
     * @param QueryFactory             $queryFactory          Query factory.
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param array                    $data                  Data.
     */
    public function __construct(
        TemplateContext $context,
        QueryFactory $queryFactory,
        SellerCollectionFactory $sellerCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->queryFactory   = $queryFactory;
        $this->sellerCollection = $this->initSellerCollection($sellerCollectionFactory);
    }

    /**
     * Returns marketplace seller collection.
     *
     * @return \Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\Collection
     */
    public function getSellerCollection()
    {
        return $this->sellerCollection;
    }

    /**
     * Returns collection size.
     *
     * @return int|null
     */
    public function getResultCount()
    {
        return $this->getSellerCollection()->getSize();
    }

    /**
     * Returns query.
     *
     * @return \Magento\Search\Model\Query
     */
    public function getQuery()
    {
        return $this->queryFactory->get();
    }

    /**
     * Returns query text.
     *
     * @return string
     */
    public function getQueryText()
    {
        return $this->getQuery()->getQueryText();
    }

    /**
     * Init seller seller collection.
     *
     * @param SellerCollectionFactory $collectionFactory Marketplace seller collection.
     *
     * @return mixed
     */
    private function initSellerCollection($collectionFactory)
    {
        $sellerCollection = $collectionFactory->create();

        $queryText = $this->getQueryText();
        $sellerCollection->addSearchFilter($queryText);

        return $sellerCollection;
    }
}
