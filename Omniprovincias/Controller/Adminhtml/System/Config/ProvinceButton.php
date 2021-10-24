<?php

namespace Omnipro\Omniprovincias\Controller\Adminhtml\System\Config;

use Magento\Directory\Model\RegionFactory;
use Omnipro\Omniprovincias\Helper\ConfigHelper;
use Psr\Log\LoggerInterface;

class ProvinceButton extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Omnipro_Omniprovincias::omniprovincias';

    const PAGE_TITLE = 'Page Title';

    protected $request;
    protected $curl;
    protected $configHelper;
    protected $regionFactory;
    protected $logger;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\HTTP\Client\Curl $curl,
        ConfigHelper $configHelper,
        RegionFactory $regionFactory,
        LoggerInterface $logger
    ) {
        $this->_pageFactory = $pageFactory;
        $this->request = $request;
        $this->curl = $curl;
        $this->configHelper = $configHelper;
        $this->regionFactory = $regionFactory;
        $this->logger = $logger;
        return parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(static::PAGE_TITLE));
        $countryId = $this->request->getParam('country');

        $regions = $this->getRegionsByCountry($countryId);
        $this->setRegionsByCountry($regions);

        $this->logger->debug('Llego a la consola los datos', ['object' => $regions]);
        return $resultPage;
    }

    /**
     * Is the user allowed to view the page.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(static::ADMIN_RESOURCE);
    }


    public function getRegionsByCountry($countryId)
    {
        $resultRegions = [];
        $uri = "https://spott.p.rapidapi.com/places?country=" . $countryId . "&type=ADMIN_DIVISION_1";
        $headers = [
            'x-rapidapi-host' => 'spott.p.rapidapi.com',
            'x-rapidapi-key' => $this->configHelper->getAPIKey()
        ];
        $this->curl->setHeaders($headers);
        $this->curl->get($uri);
        $countryRegions = json_decode($this->curl->getBody(), true);
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

    public function setRegionsByCountry($regions)
    {
        foreach ($regions as $region) {
            $this->logger->debug('region: ->', ['object' => $region]);
            $regionFactory = $this->regionFactory->create();
            $regionFactory->setCountryId($region['country_id']);
            $regionFactory->setCode($region['code']);
            $regionFactory->setDefaultName($region['default_name']);
            $regionFactory->save();
        }
    }
}
