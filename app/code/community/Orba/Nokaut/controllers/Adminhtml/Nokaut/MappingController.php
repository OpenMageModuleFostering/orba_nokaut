<?php
class Orba_Nokaut_Adminhtml_Nokaut_MappingController extends Mage_Adminhtml_Controller_Action {
	
    protected function _initAction() {
		return $this;
	}
    
    protected function _isAllowed() {
        $session = Mage::getSingleton('admin/session');
        return $session->isAllowed('catalog/nokaut/mapping_index');
    }

	public function indexAction() {
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Nokaut.pl'))
            ->_title($this->__('Mass Categories Mapping'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function newAction() {
        $this->_forward('edit');
    }
    
    public function editAction () {
        $model = Mage::getModel('nokaut/mapping');
        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }
        Mage::register('_current_mapping', $model);
        $this->loadLayout();
        $this->_setActiveMenu('nokaut/mapping');
        if ($model->getId()) {
            $breadcrumb_title = Mage::helper('nokaut')->__('Edit Mapping');
            $breadcrumb_label = $breadcrumb_title;
        }
        else {
            $breadcrumb_title = Mage::helper('nokaut')->__('New Mapping');
            $breadcrumb_label = Mage::helper('nokaut')->__('Create Mapping');
        }
        $this->_title($breadcrumb_title);
        $this->_addBreadcrumb($breadcrumb_label, $breadcrumb_title);
        // restore data
        if ($values = $this->_getSession()->getData('mapping_form_data', true)) {
            $model->addData($values);
        }
        if ($edit_block = $this->getLayout()->getBlock('mapping_edit')) {
            $edit_block->setEditMode($model->getId() > 0);
        }
        $this->renderLayout();
    }
    
    public function saveAction () {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/mapping'));
        }
        $mapping = Mage::getModel('nokaut/mapping');
        if ($id = (int)$request->getParam('id')) {
            $mapping->load($id);
        }
        $redirected = false;
        try {
            $mapping->addData($request->getParams());
            $mapping->save();
            $mapping->saveCatalogCategories();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nokaut')->__('The mapping has been saved.'));
            $this->_redirect('*/*');
            $redirected = true;
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('nokaut')->__('An error occurred while saving this mapping.'));
            $this->_getSession()->setData('mapping_form_data', $this->getRequest()->getParams());
        }
        if (!$redirected) {
            $this->_forward('new');
        }
    }

    public function deleteAction() {
        $mapping = Mage::getModel('nokaut/mapping')
            ->load($this->getRequest()->getParam('id'));
        if ($mapping->getId()) {
            $success = false;
            try {
                $mapping->delete();
                $success = true;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('nokaut')->__('An error occurred while deleting this mapping.'));
            }
            if ($success) {
                $this->_getSession()->addSuccess(Mage::helper('nokaut')->__('The mapping has been deleted.'));
            }
        }
        $this->_redirect('*/*');
    }
    
    protected function run($mapping) {
        if ($mapping->getId()) {
            $mapping->setCatalogCategoriesIds();
            $category_ids = $mapping->getCatalogCategoriesIds();
            if (!empty($category_ids)) {
                $product = Mage::getModel('nokaut/product');
                $product_ids = $product->getIdsByCategoryIds($category_ids);
                return $product->updateNokautCategory($product_ids, $mapping->getNokautCategoryId());
            }
            return true;
        }
        return false;
    }
    
    public function runAction() {
        ini_set('max_execution_time', 0);
        $mapping = Mage::getModel('nokaut/mapping')
            ->unsetData()
            ->load($this->getRequest()->getParam('id'));
        if ($this->run($mapping)) {
            $this->_getSession()->addSuccess(Mage::helper('nokaut')->__('The mapping has been finished successfully.'));
        }
        $this->_redirect('*/*');
    }
    
    public function runallAction() {
        ini_set('max_execution_time', 0);
        $mapping_collection = Mage::getModel('nokaut/mapping')->getCollection()
            ->setOrder('priority', 'ASC');
        $error = false;
        foreach ($mapping_collection as $mapping) {
            if (!$this->run($mapping)) {
                $error = true;
                break;
            }
        }
        if (!$error) {
            $this->_getSession()->addSuccess(Mage::helper('nokaut')->__('The mapping has been finished successfully.'));
        }
        $this->_redirect('*/*');
    }
    
}