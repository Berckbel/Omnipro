<?php
namespace Omnipro\Omniprovincias\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CityButton extends Field
{
    protected $_template = 'Omnipro_Omniprovincias::system/config/city_button.phtml';

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function render(
        AbstractElement $element
    ) {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(
        AbstractElement $element
    ) {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->getUrl('omniprovincias/system_config/cityButton');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(['id' => 'city_button', 'label' => __('Update City'),]);
        return $button->toHtml();
    }
}
