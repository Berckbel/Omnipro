<?php
namespace Omnipro\Omniprovincias\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionFactory;
use Omnipro\Omniprovincias\Model\ResourceModel\City\CollectionFactory as CityFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Omnipro\Omniprovincias\Helper\ConfigHelper;

class CountryCityInstalled implements OptionSourceInterface
{

    protected $configHelper;
    protected $regionFactory;
    protected $countryFactory;
    protected $cityFactory;
    public $data;

    public function __construct(
        ConfigHelper $configHelper,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        CityFactory $cityFactory
    )
    {
        $this->configHelper = $configHelper;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
        $this->cityFactory = $cityFactory;

        $this->data = $this->getCountriesWithCity();
    }

    public function toOptionArray()
    {
        
        return $this->data;
    }

    public function getCountriesWithCity()
    {
        $result = [];
        $countries = $this->countryFactory->create();
        $codes = $this->getCountryCodesInCity();
        foreach($countries as $country){
            if(in_array($country->getData('country_id'), $codes)){
                $result[] = ['value'=> $country->getData('country_id'), 'label'=> $country->getName()];
            }
        }
        return $result;
    }

    public function getCountryCodesInCity(){
        $result = [];
        $cities = $this->cityFactory->create();
        foreach($cities as $city){
            $result[] = $city->getData('country_code');
        }
        $result = array_unique($result);
        return $result;
    }

}
