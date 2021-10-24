<?php
namespace Omnipro\Omniprovincias\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigHelper
{
    const XML_PATH_COUNTRY = 'omniprovincias/provinces/country';
    const XML_PATH_APYKEY = 'omniprovincias/general/api_key';
    protected $config;

    public function __construct(
        ScopeConfigInterface $config
    )
    {
        $this->config = $config;
    }

    public function selectedCountry(){
        return $this->config->getValue(self::XML_PATH_COUNTRY);
    }

    public function getAPIKey(){
        return $this->config->getValue(self::XML_PATH_APYKEY);
    }
}
