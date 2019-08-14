<?php
class Orba_Nokaut_Model_Mysql4_Mapping_Catalog_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    
    protected function _construct(){
        parent::_construct();
        $this->_init('nokaut/mapping_catalog_category');
    }
    
}
