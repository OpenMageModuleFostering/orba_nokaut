<?php
class Orba_Nokaut_Block_Admin_Offer_Urls extends Mage_Adminhtml_Block_Widget_Container {
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('nokaut/offer/urls.phtml');
    }

    protected function _prepareLayout() {
        $this->setChild('grid', $this->getLayout()->createBlock('nokaut/admin_offer_urls_grid', 'nokaut_offer_urls_grid'));
        return parent::_prepareLayout();
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
    
}
