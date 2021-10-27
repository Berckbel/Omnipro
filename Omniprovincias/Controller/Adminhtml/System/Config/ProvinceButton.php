<?php

namespace Omnipro\Omniprovincias\Controller\Adminhtml\System\Config;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Directory\Model\CountryFactory;
use Omnipro\Omniprovincias\Helper\ConfigHelper;
use Psr\Log\LoggerInterface;

class ProvinceButton extends Action
{
    const ADMIN_RESOURCE = 'Omnipro_Omniprovincias::omniprovincias';

    const PAGE_TITLE = 'Page Title';

    protected $_request;
    protected Curl $_curl;
    protected ConfigHelper $_configHelper;
    protected RegionFactory $_regionFactory;
    protected CountryFactory $_countryFactory;
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
        ManagerInterface $messageManager,
        LoggerInterface  $logger
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_request = $request;
        $this->_curl = $curl;
        $this->_configHelper = $configHelper;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
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

        $regions = $this->getRegionsByCountry($countryId);
        $this->setRegions($regions);

        $this->_messageManager->addSuccessMessage(__("Regions have been loaded successfully."));
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
    public function getRegionsByCountry(string $countryId): array
    {
        $resultRegions = [];
        $uri = "https://spott.p.rapidapi.com/places?country=" . $countryId . "&type=ADMIN_DIVISION_1";
        $headers = [
            'x-rapidapi-host' => 'spott.p.rapidapi.com',
            'x-rapidapi-key' => $this->_configHelper->getAPIKey()
        ];
        $this->_curl->setHeaders($headers);
        $this->_curl->get($uri);
        $countryRegions = json_decode($this->_curl->getBody(), true);

        foreach ($countryRegions as $region) {
            $bind = [
                'country_id' => $countryId,
                'code' => $region['id'],
                'default_name' => $region['name']
            ];
            $resultRegions[] = $bind;
        }

        return $resultRegions;
    }

    /**
     * @param array $regions
     * @throws Exception
     */
    public function setRegions(array $regions)
    {
        foreach ($regions as $region) {
            $regionFactory = $this->_regionFactory->create();
            $regionModel = $regionFactory->loadByCode($region['code'], $region['country_id']);
            if (!$regionModel->getId()) {
                $regionFactory->setCountryId($region['country_id']);
                $regionFactory->setCode($region['code']);
                $regionFactory->setDefaultName($region['default_name']);
                $regionFactory->save();
            }
        }
    }
}
