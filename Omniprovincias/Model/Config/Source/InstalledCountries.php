<?php
namespace Omnipro\Omniprovincias\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Omnipro\Omniprovincias\Helper\ConfigHelper;

class InstalledCountries implements OptionSourceInterface
{
    protected $configHelper;
    protected $regionFactory;
    protected $countryFactory;
    public $data;

    public function __construct(
        ConfigHelper $configHelper,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory
    )
    {
        $this->configHelper = $configHelper;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;

        $this->data = $this->getCountriesWithRegions();
    }

    public function toOptionArray()
    {
        
        return $this->data;
    }

    public function getCountriesWithRegions()
    {
        $result = [];
        $countries = $this->countryFactory->create();
        $codes = $this->getCountryCodesInRegions();
        foreach($countries as $country){
            if(in_array($country->getData('country_id'), $codes)){
                $result[] = ['value'=> $country->getData('country_id'), 'label'=> $country->getName()];
            }
        }
        return $result;
    }

    public function getCountryCodesInRegions(){
        $result = [];
        $regions = $this->regionFactory->create();
        foreach($regions as $region){
            $result[] = $region->getData('country_id');
        }
        $result = array_unique($result);
        return $result;
    }

}
