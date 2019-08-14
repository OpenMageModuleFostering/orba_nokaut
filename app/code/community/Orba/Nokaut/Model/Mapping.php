<?php
class Orba_Nokaut_Model_Mapping extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        $this->_init('nokaut/mapping');
    } 
    
    protected function getConfig() {
        return Mage::getModel('nokaut/config');
    }
    
    public function saveCatalogCategories() {
        if ($this->getId()) {     
            $model = Mage::getModel('nokaut/mapping_catalog_category');
            $ids = $this->getCategoryIds() ? $this->getCategoryIds() : array();
            $collection = $model->getCollection()
                ->addFieldToFilter('mapping_id', $this->getId());
            $old_ids = array();
            foreach ($collection as $item) {
                $old_ids[] = $item->getCategoryId();
            }
            $ids_to_delete = array_diff($old_ids, $ids);
            if (!empty($ids_to_delete)) {
                $collection_to_delete = $model->getCollection()
                    ->addFieldToFilter('mapping_id', $this->getId())
                    ->addFieldToFilter('category_id', array('in' => $ids_to_delete));
                foreach ($collection_to_delete as $item) {
                    $item->delete();
                }
            }
            $ids_to_save = array_diff($ids, $old_ids);
            if (!empty($ids_to_save)) {
                foreach ($ids_to_save as $category_id) {
                    $item = $model->setId(null)
                        ->setMappingId($this->getId())
                        ->setCategoryId($category_id)
                        ->save();
                }
            }
        }
    }
    
    public function setCatalogCategoriesIds() {
        if ($this->getId()) {
            $collection = Mage::getModel('nokaut/mapping_catalog_category')->getCollection()
                ->addFieldToFilter('mapping_id', $this->getId());
            $ids = array();
            foreach ($collection as $item) {
                $ids[] = $item->getCategoryId();
            }
            $this->setData('catalog_categories_ids', $ids);
        }
    }
    
}
