<?php

namespace Omnipro\Omniprovincias\Controller\Adminhtml\System\Config;

use Psr\Log\LoggerInterface;

class ProvinceButton extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Omnipro_Omniprovincias::omniprovincias';

    const PAGE_TITLE = 'Page Title';

    protected $request;
    protected $curl;
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
        LoggerInterface $logger
    ) {
        $this->_pageFactory = $pageFactory;
        $this->request = $request;
        $this->curl = $curl;
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
        // $resultPage->setActiveMenu(static::ADMIN_RESOURCE);
        // $resultPage->addBreadcrumb(__(static::PAGE_TITLE), __(static::PAGE_TITLE));
        $resultPage->getConfig()->getTitle()->prepend(__(static::PAGE_TITLE));
        $countryId = $this->request->getParam('country');

        $data = $this->getRegionsByCountry($countryId);

        $this->logger->debug('Llego al consola los datos', ['object' => $data[0]]);
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


    public function getRegionsByCountry($countryId){
        $uri = "https://spott.p.rapidapi.com/places?country=" . $countryId;
        $headers = [
            'x-rapidapi-host'=>'spott.p.rapidapi.com',
            'x-rapidapi-key'=>'03fd18e644msh006192363c01dacp1a8d13jsn8cdd518ebf55'
        ];
        $this->curl->setHeaders($headers);
        $this->curl->get($uri);
        return json_decode($this->curl->getBody(), true);
    }
}
