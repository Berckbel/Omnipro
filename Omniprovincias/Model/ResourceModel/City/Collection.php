<?php
namespace Omnipro\Omniprovincias\Model\ResourceModel\City;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'city_id';
    protected $_eventPrefix = 'omnipro_omniprovincias_city_collection';
    protected $_eventObject = 'city_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnipro\Omniprovincias\Model\City', 'Omnipro\Omniprovincias\Model\ResourceModel\City');
    }
}
