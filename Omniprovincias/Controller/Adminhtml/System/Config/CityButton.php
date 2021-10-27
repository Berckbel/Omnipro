<?php

namespace Omnipro\Omniprovincias\Controller\Adminhtml\System\Config;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Directory\Model\RegionFactory;
use Omnipro\Omniprovincias\Model\CityFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Directory\Model\CountryFactory;
use Omnipro\Omniprovincias\Helper\ConfigHelper;
use Psr\Log\LoggerInterface;

class CityButton extends Action
{
    const ADMIN_RESOURCE = 'Omnipro_Omniprovincias::omniprovincias';

    const PAGE_TITLE = 'Page Title';

    protected $_request;
    protected Curl $_curl;
    protected ConfigHelper $_configHelper;
    protected RegionFactory $_regionFactory;
    protected CountryFactory $_countryFactory;
    protected CityFactory $_cityFactory;
    protected LoggerInterface $_logger;
    protected PageFactory $_pageFactory;
    protected ManagerInterface $_messageManager;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Http $request
     * @param Curl $curl
     * @param ConfigHelper $configHelper
     * @param RegionFactory $regionFactory
     * @param CountryFactory $countryFactory
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context          $context,
        PageFactory      $pageFactory,
        Http             $request,
        Curl             $curl,
        ConfigHelper     $configHelper,
        RegionFactory    $regionFactory,
        CountryFactory   $countryFactory,
        CityFactory      $cityFactory,
        ManagerInterface $messageManager,
        LoggerInterface  $logger
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_request = $request;
        $this->_curl = $curl;
        $this->_configHelper = $configHelper;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        $this->_cityFactory = $cityFactory;
        $this->_messageManager = $messageManager;
        $this->_logger = $logger;
        return parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return Page
     * @throws Exception
     */
    public function execute(): Page
    {
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(static::PAGE_TITLE));
        $countryId = $this->_request->getParam('country');

        $cities = $this->getCitiesByCountry($countryId);

        $this->_logger->debug('ciudades devueltas', ['object' => $cities]);

        $this->setCities($cities);

        $this->_messageManager->addSuccessMessage(__("Cities have been loaded successfully."));
        return $resultPage;
    }

    /**
     * Is the user allowed to view the page.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(static::ADMIN_RESOURCE);
    }

    /**
     * @param string $countryId
     * @return array
     */
    public function getCitiesByCountry(string $countryId): array
    {
        $resultCities = [];
        $uri = "https://spott.p.rapidapi.com/places?country=" . $countryId . "&type=CITY";
        $headers = [
            'x-rapidapi-host' => 'spott.p.rapidapi.com',
            'x-rapidapi-key' => $this->_configHelper->getAPIKey()
        ];
        $this->_curl->setHeaders($headers);
        $this->_curl->get($uri);

        $countryCities = json_decode($this->_curl->getBody(), true);

        foreach ($countryCities as $city) {
            $region_name = $city['adminDivision1']['name'];
            $bind = [
                'region_name' => $region_name,
                'country_code' => $countryId,
                'code' => $city['id'],
                'city_name' => $city['name']
            ];
            $resultCities[] = $bind;
        }

        return $resultCities;
    }

    /**
     * @param array $regions
     * @throws Exception
     */
    public function setCities(array $cities)
    {
        foreach ($cities as $city) {
            $codesCountry = $this->getCodesInCity();
            if(!in_array($city['code'], $codesCountry)){
                $cityFactory = $this->_cityFactory->create();
                $cityFactory->setData('region_name', $city['region_name']);
                $cityFactory->setData('country_code', $city['country_code']);
                $cityFactory->setData('code', $city['code']);
                $cityFactory->setData('city_name', $city['city_name']);
                $cityFactory->save();
            }
        }
    }

    public function getCodesInCity(){
        $result = [];
        $cities = $this->_cityFactory->create()->getCollection();
        foreach($cities as $city){
            $result[] = $city->getData('code');
        }
        $result = array_unique($result);
        return $result;
    }
}
