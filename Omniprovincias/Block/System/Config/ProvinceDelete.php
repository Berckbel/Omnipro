<?php
namespace Omnipro\Omniprovincias\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

class ProvinceDelete extends Field
{
    protected $_template = 'Omnipro_Omniprovincias::system/config/province_delete.phtml';

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(
        AbstractElement $element
    ): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(
        AbstractElement $element
    ): string
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getCustomUrl(): string
    {
        return $this->getUrl('omniprovincias/system_config/provinceDelete');
    }

    /**
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(['id' => 'province_delete', 'label' => __('Delete Provinces'),]);
        return $button->toHtml();
    }
}
