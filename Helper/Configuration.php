<?php

namespace Lof\ElasticsuiteSeller\Helper;

use Smile\ElasticsuiteCore\Helper\AbstractConfiguration;


class Configuration extends AbstractConfiguration
{
    /**
     * Location of Elasticsuite marketplace seller settings configuration.
     *
     * @var string
     */
    const CONFIG_XML_PREFIX = 'lof_elasticsuite_seller/seller_settings';

    /**
     * Retrieve a configuration value by its key
     *
     * @param string $key The configuration key
     *
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue(self::CONFIG_XML_PREFIX . "/" . $key);
    }

    /**
     * Is enabled module
     * 
     * @return int|bool
     */
    public function isEnabled()
    {
        return (int)$this->getConfigValue("enabled");
    }
}
