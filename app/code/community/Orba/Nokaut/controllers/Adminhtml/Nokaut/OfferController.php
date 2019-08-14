<?php
class Orba_Nokaut_Adminhtml_Nokaut_OfferController extends Mage_Adminhtml_Controller_Action {
	
    protected function _initAction() {
		return $this;
	}
        
    protected function _isAllowed() {
        $session = Mage::getSingleton('admin/session');
        return $session->isAllowed('catalog/nokaut/offer_' . $this->getRequest()->getActionName());
    }

	public function indexAction() {
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Nokaut.pl'))
            ->_title($this->__('Offer'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function urlsAction() {
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Nokaut.pl'))
            ->_title($this->__('Feed URLs'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
}