<?php

namespace Omnipro\Omniprovincias\Controller\Adminhtml\System\Config;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Directory\Model\CountryFactory;
use Omnipro\Omniprovincias\Helper\ConfigHelper;
use Psr\Log\LoggerInterface;

class ProvinceDelete extends Action
{
    const ADMIN_RESOURCE = 'Omnipro_Omniprovincias::omniprovincias';

    const PAGE_TITLE = 'Page Title';

    protected $_request;
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
        ConfigHelper     $configHelper,
        RegionFactory    $regionFactory,
        CountryFactory   $countryFactory,
        ManagerInterface $messageManager,
        LoggerInterface  $logger
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_request = $request;
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

        $this->deleteRegionsByCountry($countryId);

        $this->_messageManager->addSuccessMessage(__("Regions have been deleted successfully."));
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
     * @return void
     */
    public function deleteRegionsByCountry(string $countryId)
    {
        $regions = $this->_regionFactory->create()->getCollection();
        foreach ($regions as $region) {
            if ($region->getData('country_id') == $countryId) {
                $region->delete();
            }
        }
    }
}
