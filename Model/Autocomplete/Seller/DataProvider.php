<?php

namespace Lof\ElasticsuiteSeller\Model\Autocomplete\Seller;

use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticsuiteCore\Helper\Autocomplete as ConfigurationHelper;
use Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\CollectionFactory as SellerCollectionFactory;
use Smile\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider as TermDataProvider;

/**
 * Catalog product autocomplete data provider.
 *
 */
class DataProvider implements DataProviderInterface
{
    /**
     * Autocomplete type
     */
    const AUTOCOMPLETE_TYPE = "marketplace_seller";

    /**
     * Autocomplete result item factory
     *
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * Query factory
     *
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var TermDataProvider
     */
    protected $termDataProvider;

    /**
     * @var CmsCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string Autocomplete result type
     */
    private $type;

    /**
     * Max sellers autocomplete search results
     * @var int
     */
    protected $maxAutocompleteResults = 5;

    /**
     * Constructor.
     *
     * @param ItemFactory           $itemFactory          Suggest item factory.
     * @param QueryFactory          $queryFactory         Search query factory.
     * @param TermDataProvider      $termDataProvider     Search terms suggester.
     * @param SellerCollectionFactory  $sellerCollectionFactory Seller collection factory.
     * @param ConfigurationHelper   $configurationHelper  Autocomplete configuration helper.
     * @param StoreManagerInterface $storeManager         Store manager.
     * @param string                $type                 Autocomplete provider type.
     */
    public function __construct(
        ItemFactory $itemFactory,
        QueryFactory $queryFactory,
        TermDataProvider $termDataProvider,
        SellerCollectionFactory $sellerCollectionFactory,
        ConfigurationHelper $configurationHelper,
        StoreManagerInterface $storeManager,
        $type = self::AUTOCOMPLETE_TYPE
    ) {
        $this->itemFactory          = $itemFactory;
        $this->queryFactory         = $queryFactory;
        $this->termDataProvider     = $termDataProvider;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->configurationHelper  = $configurationHelper;
        $this->type                 = $type;
        $this->storeManager         = $storeManager;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        $result = [];
        $sellerCollection = $this->getSellerCollection();
        $i = 0;
        if ($sellerCollection) {
            /** @var \Lof\MarketPlace\Model\seller $seller */
            foreach ($sellerCollection as $seller) {
                $result[] = $this->itemFactory->create([
                        'title' => $seller->getTitle(),
                        'url'   => $seller->getUrl(),
                        'type' => $this->getType()]);
                $i++;
                if ($i == $this->maxAutocompleteResults) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * List of search terms suggested by the search terms data daprovider.
     *
     * @return array
     */
    private function getSuggestedTerms()
    {
        $terms = array_map(
            function (\Magento\Search\Model\Autocomplete\Item $termItem) {
                return $termItem->getTitle();
            },
            $this->termDataProvider->getItems()
        );

        return $terms;
    }

    /**
     * Suggested seller collection.
     * Returns null if no suggested search terms.
     *
     * @return \Lof\ElasticsuiteSeller\Model\ResourceModel\Seller\Fulltext\Collection|null
     */
    private function getSellerCollection()
    {
        $pageCollection = null;
        $suggestedTerms = $this->getSuggestedTerms();
        $terms          = [$this->queryFactory->get()->getQueryText()];

        if (!empty($suggestedTerms)) {
            $terms = array_merge($terms, $suggestedTerms);
        }

        $sellerCollection = $this->sellerCollectionFactory->create();
        $sellerCollection->addSearchFilter($terms);
        $sellerCollection->setPageSize($this->getResultsPageSize());

        return $sellerCollection;
    }

    /**
     * Retrieve number of pages to display in autocomplete results
     *
     * @return int
     */
    private function getResultsPageSize()
    {
        return $this->configurationHelper->getMaxSize($this->getType());
    }
}
