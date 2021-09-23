<?php

namespace Lof\ElasticsuiteSeller\Block\Seller;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Search\Model\QueryFactory;
use Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\CollectionFactory as SellerCollectionFactory;
use Lof\ElasticsuiteSeller\Helper\Configuration;

class Suggest extends \Magento\Framework\View\Element\Template
{
    /**
     * Name of field to get max results.
     *
     * @var string
     */
    const MAX_RESULT = 'max_result';

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Configuration
     */
    private $helper;

    /**
     * @var \Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\Collection
     */
    private $sellerCollection;

    /**
     * Suggest constructor.
     *
     * @param TemplateContext          $context               Template contexte.
     * @param QueryFactory             $queryFactory          Query factory.
     * @param SellerCollectionFactory  $sellerCollectionFactory  Seller collection factory.
     * @param Configuration            $helper                Configuration helper.
     * @param array                    $data                  Data.
     */
    public function __construct(
        TemplateContext $context,
        QueryFactory $queryFactory,
        SellerCollectionFactory $sellerCollectionFactory,
        Configuration $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->queryFactory   = $queryFactory;
        $this->helper         = $helper;
        $this->sellerCollection = $this->initSellerCollection($sellerCollectionFactory);
    }

    /**
     * Returns if block can be display.
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->getResultCount() > 0;
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
     * Returns number of results.
     *
     * @return int
     */
    public function getNumberOfResults()
    {
        return $this->helper->getConfigValue(self::MAX_RESULT);
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
     * Returns all results url page.
     *
     * @return string
     */
    public function getShowAllUrl()
    {
        return $this->getUrl('elasticsuiteseller/result', ['q' => $this->getQueryText()]);
    }

    /**
     * Init marketplace seller collection.
     *
     * @param SellerCollectionFactory $collectionFactory Marketplace Seller collection.
     *
     * @return mixed
     */
    private function initSellerCollection($collectionFactory)
    {
        $sellerCollection = $collectionFactory->create();

        $sellerCollection->setPageSize($this->getNumberOfResults());

        $queryText = $this->getQueryText();
        $sellerCollection->addSearchFilter($queryText);

        return $sellerCollection;
    }
}
