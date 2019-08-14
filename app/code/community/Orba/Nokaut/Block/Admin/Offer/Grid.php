<?php
class Orba_Nokaut_Block_Admin_Offer_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('nokaut_offer_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
    }
    
    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _prepareCollection(){
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('nokaut_category_id', 'left');
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU '),
            'align' => 'right',
            'width' => '100px',
            'index' => 'sku',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));
        $nokaut_categories = Mage::getModel('nokaut/category')->toOptionHash();
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
        $this->addColumn('action', array(
            'header' => Mage::helper('catalog')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('catalog')->__('Edit'),
                    'url' => array(
                        'base'=>'adminhtml/catalog_product/edit',
                        'params'=>array('store'=>$this->getRequest()->getParam('store'))
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
        if ($value && !empty($value) && $value != 'null') {
            $ids = array($value);
            $this->getCollection()->addAttributeToFilter('nokaut_category_id', array(
                'in' => $ids,
                'notnull' => true,
                'neq' => ''
            ));
        } else if ($value == 'null') {
            $this->getCollection()->addAttributeToFilter(array(
                array('attribute' => 'nokaut_category_id', 'null' => true),
                array('attribute' => 'nokaut_category_id', 'eq' => '')
            ));
        }
    }
    
    public function getRowUrl($row) {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    
}