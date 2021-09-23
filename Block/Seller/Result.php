<?php


namespace Lof\ElasticsuiteSeller\Block\Seller;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Search\Model\QueryFactory;
use Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\CollectionFactory as SellerCollectionFactory;

class Result extends \Lof\MarketPlace\Block\Sellerpage
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\Collection|null
     */
    private $sellerCollection = null;

    /**
     * @var SellerCollectionFactory
     */
    private $sellerCollectionFactory;

    /**
     * Sellerpage constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param \Lof\MarketPlace\Model\Rating $rating
     * @param QueryFactory $queryFactory
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Lof\MarketPlace\Model\Rating $rating,
        QueryFactory $queryFactory,
        SellerCollectionFactory $sellerCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $sellerHelper, $seller, $orderitems, $rating, $data);
        $this->queryFactory   = $queryFactory;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->sellerCollection = $this->initSellerCollection($this->sellerCollectionFactory);
        $this->setCollection($this->sellerCollection);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        if ($this->sellerCollection) {
            $this->setCollection($this->sellerCollection);
        }
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__("Search results for: '%1'", $this->getQueryText()));
        return $this;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        if ($breadcrumbsBlock) {

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'lofmarketplace',
                [
                    'label' => __("Results in marketplace vendor."),
                    'title' => __("Results in marketplace vendor."),
                    'link' => ''
                ]
            );
        }
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
