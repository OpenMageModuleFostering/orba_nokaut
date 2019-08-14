<?php
class Orba_Nokaut_Model_Category extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        $this->_init('nokaut/category');
    } 
    
    protected function getConfig() {
        return Mage::getModel('nokaut/config');
    }
    
    public function getAllOptions($empty = true) {
        $cache_id = 'nokaut_categories_option_array';
        if (false !== ($data = Mage::app()->getCache()->load($cache_id))) {
            $options = unserialize($data);
        } else {
            $options = $this->getFlatTree();
            Mage::app()->getCache()->save(serialize($options), $cache_id);
        }
        if ($empty) {
            $options = array_merge(array(array('label' => '', 'value' => '')), $options);
        }
        return $options;
    }
    
    public function getFlatTree() {
        $res = array();
        $category_collection = $this->getCollection()
            ->setOrder('name', 'asc');
        foreach ($category_collection as $category) {
            $res[] = array(
                'label' => ($category->getIsDeleted() ? '['.Mage::helper('nokaut')->__('Deleted').'] ' : '').$category->getName(),
                'value' => $category->getId()
            );
        }
        return $res;
    }
    
    public function toOptionHash($empty = true) {
        $e = $empty ? array(array('label' => Mage::helper('nokaut')->__('not set'), 'value' => 'null')) : array();
        $options = array_merge($e, $this->getAllOptions(false));
        $option_hash = array();
        foreach ($options as $option) {
            $option_hash[$option['value']] = $option['label'];
        }
        return $option_hash;
    }
    
    public function getFlatColums() {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null
        );
        $helper = Mage::helper('core');
        if (!method_exists($helper, 'useDbCompatibleMode') || $helper->useDbCompatibleMode()) {
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_INTEGER;
            $column['nullable'] = true;
        }
        return array($attributeCode => $column);
    }
    
    public function getFlatUpdateSelect($store) {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
    
    public function getOptionText($value) {
        $options = $this->getAllOptions();
        if (sizeof($options) > 0) foreach($options as $option) {
            if (isset($option['value']) && $option['value'] == $value) {
                return isset($option['label']) ? $option['label'] : $option['value'];
            }
        }
        if (isset($options[$value])) {
            return $options[$value];
        }
        return false;
    }

    public function getOptionId($value) {
        foreach ($this->getAllOptions() as $option) {
            if (strcasecmp($option['label'], $value)==0 || $option['value'] == $value) {
                return $option['value'];
            }
        }
        return null;
    }
    
}