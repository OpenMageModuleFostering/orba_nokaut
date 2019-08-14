<?php
class Orba_Nokaut_Block_Admin_Mapping_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('nokaut_mapping_grid');
        $this->setDefaultSort('priority');
        $this->setDefaultDir('desc');
    }
    
    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _prepareCollection(){
        $store = $this->_getStore();
        $collection = Mage::getModel('nokaut/mapping')->getCollection();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('nokaut')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));
        $nokaut_categories = Mage::getModel('nokaut/category')->toOptionHash(false);
        $this->addColumn('nokaut_category_id', array(
            'header' => Mage::helper('nokaut')->__('Nokaut Category'),
            'align' => 'left',
            'index' => 'nokaut_category_id',
            'type' => 'options',
            'options' => $nokaut_categories,
            'filter_condition_callback' => array(
                $this,
                '_filterNokautCategoriesCondition'
            )
        ));
        $this->addColumn('priority', array(
            'header' => Mage::helper('nokaut')->__('Priority'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'priority',
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('catalog')->__('Action'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('catalog')->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('nokaut')->__('Run'),
                    'url' => array(
                        'base' => '*/*/run'
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
        ));
        return parent::_prepareColumns();
    }
    
    protected function _filterNokautCategoriesCondition($collection, $column) {
        $value = $column->getFilter()->getValue();
        if ($value && !empty($value)) {
            $ids = array($value);
            $this->getCollection()->addFieldToFilter('nokaut_category_id', array(
                'in' => $ids,
                'notnull' => true,
                'neq' => ''
            ));
        }
    }
    
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array(
            'store' => $this->getRequest()->getParam('store'),
            'id' => $row->getId())
        );
    }
    
}