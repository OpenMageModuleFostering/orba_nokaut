<?php
class Orba_Nokaut_Model_Config extends Mage_Core_Model_Abstract {
    
    public static $groups = array(
        'conditions' => array('availability', 'set'),
        'mappings' => array('producer', 'promo', 'warranty')
    );
    
    const AVAILABILITY_DEFAULT_VALUE = 4;

    public function _construct() {
        $this->_init('nokaut/config');
    }
    
    public function getGroups() {
        return self::$groups;
    }
    
    public function getCoreAttributesConditions() {
        $attributes = self::$groups['conditions'];
        $res = array();
        foreach ($attributes as $attr) {
            $res[$attr] = $this->getAttributeConditions($attr, 'core');
        }
        return $res;
    }
    
    public function getAttributeConditions($attr, $group) {
        if ($group == 'core' && $attr == 'availability') {
            $values = array(0, 1, 2, 3);
            $res = array(
                'values' => array(),
                'default' => self::AVAILABILITY_DEFAULT_VALUE
            );
            foreach ($values as $value) {
                $res['values'][$value] = array(
                    'code' => Mage::getStoreConfig('nokaut/attr_core/availability_'.$value.'_name'),
                    'value' => Mage::getStoreConfig('nokaut/attr_core/availability_'.$value.'_value')
                );
            }
            return $res;
        } else {
            return array(
                'code' => Mage::getStoreConfig('nokaut/attr_'.$group.'/'.$attr.'_name'),
                'value' => Mage::getStoreConfig('nokaut/attr_'.$group.'/'.$attr.'_value')
            );
        }
    }
    
    public function getAttributesMappings() {
        $groups = $this->getGroups();
        $res = array();
        foreach ($groups as $group => $attributes) {
            $res[$group] = array();
            foreach ($attributes as $attr) {
                if ($group != 'conditions') {
                    $res[$group][$attr] = Mage::getStoreConfig('nokaut/attr_core/'.$attr);
                } else {
                    if ($attr != 'availability') {
                        $res[$group][$attr] = Mage::getStoreConfig('nokaut/attr_core/'.$attr.'_name');
                    } else {
                        $indexes = array(0, 1, 2, 3);
                        foreach ($indexes as $i) {
                            $res[$group][$attr.'_'.$i] = Mage::getStoreConfig('nokaut/attr_core/'.$attr.'_'.$i.'_name');
                        }
                    }
                }
            }
        }
        return $res;
    }
    
    public function getPriceIncludesTax() {
        return Mage::getStoreConfig('tax/calculation/price_includes_tax');
    }
    
    public function getStore() {
        return Mage::app()->getStore();
    }
    
    public function saveHash() {
        $hash = md5(microtime());
        Mage::getModel('core/config')->saveConfig('nokaut/config/hash', $hash, 'default', 0);
    }
    
    public function getHash() {
        return Mage::getStoreConfig('nokaut/config/hash');
    }
    
    public function isFlatCatalogEnabled() {
        return Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
    }
    
}