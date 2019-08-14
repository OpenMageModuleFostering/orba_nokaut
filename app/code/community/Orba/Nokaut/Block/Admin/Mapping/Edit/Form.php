<?php
class Orba_Nokaut_Block_Admin_Mapping_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    public function __construct() {
        parent::__construct();
    }

    public function getModel() {
        return Mage::registry('_current_mapping');
    }

    protected function _prepareForm() {
        $model = $this->getModel();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));
        // Mapping information
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('nokaut')->__('Mapping Information'),
        ));
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
                'value' => $model->getId(),
            ));
        }
        $nokaut_categories = array('' => '') + Mage::getModel('nokaut/category')->toOptionHash(false);
        $fieldset->addField('nokaut_category_id', 'select', array(
            'name' => 'nokaut_category_id',
            'label' => Mage::helper('nokaut')->__('Nokaut Category'),
            'title' => Mage::helper('nokaut')->__('Nokaut Category'),
            'required' => true,
            'value' => $model->getNokautCategoryId(),
            'options' => $nokaut_categories
        ));
        $fieldset->addField('priority', 'text', array(
            'name' => 'priority',
            'label' => Mage::helper('nokaut')->__('Priority'),
            'title' => Mage::helper('nokaut')->__('Priority'),
            'value' => $model->getPriority(),
            'class' => 'validate-digits'
        ));
        // Rules
        $fieldset = $form->addFieldset('rules', array(
            'legend' => Mage::helper('nokaut')->__('Mapping Rules'),
            'class' => 'fieldset-wide'
        ));
        $categories = Mage::getModel('nokaut/catalog_category')->getAllOptions();
        $model->setCatalogCategoriesIds();
        $fieldset->addField('category_ids', 'multiselect', array(
            'name' => 'category_ids',
            'label' => Mage::helper('nokaut')->__('Categories'),
            'title' => Mage::helper('nokaut')->__('Categories'),
            'class' => 'requried-entry',
            'value' => $model->getCatalogCategoriesIds(),
            'values' => $categories,
            'style' => 'width: 100%;'
        ));
        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
}
