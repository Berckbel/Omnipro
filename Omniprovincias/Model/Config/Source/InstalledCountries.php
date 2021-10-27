<?php
namespace Omnipro\Omniprovincias\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Omnipro\Omniprovincias\Helper\ConfigHelper;

class InstalledCountries implements OptionSourceInterface
{
    protected ConfigHelper $_configHelper;
    protected RegionFactory $_regionFactory;
    protected CountryFactory $_countryFactory;
    public array $_data;

    public function __construct(
        ConfigHelper $configHelper,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory
    )
    {
        $this->_configHelper = $configHelper;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;

        $this->_data = $this->getCountriesWithRegions();
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->_data;
    }

    /**
     * @return array
     */
    public function getCountriesWithRegions(): array
    {
        $result = [];
        $countries = $this->_countryFactory->create();
        $codes = $this->getCountryCodesInRegions();
        foreach($countries as $country){
            if(in_array($country->getData('country_id'), $codes)){
                $result[] = ['value'=> $country->getData('country_id'), 'label'=> $country->getName()];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getCountryCodesInRegions(): array
    {
        $result = [];
        $regions = $this->_regionFactory->create();
        foreach($regions as $region){
            $result[] = $region->getData('country_id');
        }
        return array_unique($result);
    }

}
