<?php
class Orba_Nokaut_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    
    protected function _construct(){
        parent::_construct();
        $this->_init('nokaut/category');
    }
    
}
