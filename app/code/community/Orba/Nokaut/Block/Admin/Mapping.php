<?php
class Orba_Nokaut_Block_Admin_Mapping extends Mage_Adminhtml_Block_Widget_Container {
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('nokaut/mapping.phtml');
    }

    protected function _prepareLayout() {
        $this->_addButton('add_new', array(
            'label'   => $this->__('Add Mapping'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class'   => 'add'
        ));
        $this->_addButton('run_all', array(
            'label'   => $this->__('Run All'),
            'onclick' => "setLocation('{$this->getUrl('*/*/runall')}')"
        ));    
        $this->setChild('grid', $this->getLayout()->createBlock('nokaut/admin_mapping_grid', 'nokaut_mapping_grid'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml() {
        return $this->getChildHtml('add_new_button');
    }
    
    public function getRunAllButtonHtml() {
        return $this->getChildHtml('run_all_button');
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
    
}
