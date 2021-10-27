<?php
namespace Omnipro\Omniprovincias\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionFactory;
use Omnipro\Omniprovincias\Model\ResourceModel\City\CollectionFactory as CityFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Omnipro\Omniprovincias\Helper\ConfigHelper;

class CountryCityInstalled implements OptionSourceInterface
{

    protected ConfigHelper $_configHelper;
    protected RegionFactory $_regionFactory;
    protected CountryFactory $_countryFactory;
    protected CityFactory $_cityFactory;
    public array $_data;

    public function __construct(
        ConfigHelper $configHelper,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        CityFactory $cityFactory
    )
    {
        $this->_configHelper = $configHelper;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        $this->_cityFactory = $cityFactory;

        $this->_data = $this->getCountriesWithCity();
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
    public function getCountriesWithCity(): array
    {
        $result = [];
        $countries = $this->_countryFactory->create();
        $codes = $this->getCountryCodesInCity();
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
    public function getCountryCodesInCity(): array
    {
        $result = [];
        $cities = $this->_cityFactory->create();
        foreach($cities as $city){
            $result[] = $city->getData('country_code');
        }
        return array_unique($result);
    }

}
