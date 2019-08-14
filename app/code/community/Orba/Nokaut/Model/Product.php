<?php

class Orba_Nokaut_Model_Product extends Mage_Catalog_Model_Product {
    
    protected function getConfig() {
        return Mage::getModel('nokaut/config');
    }
    
    public function getOffers() {
        $store = $this->getConfig()->getStore();
        $conditions = $this->getConfig()->getCoreAttributesConditions();
        $mappings = $this->getConfig()->getAttributesMappings();
        $additional_attributes = array();
        $_attribute = Mage::getModel('nokaut/attribute');
        foreach ($mappings as $group) {
            foreach ($group as $mapping) {
                if (!empty($mapping)) {
                    if (!in_array($mapping, $additional_attributes)) {
                        $additional_attributes[$mapping] = $_attribute->getOptionsByCode($mapping);
                    }
                }
            }
        }
        $product_collection = $this->getCollection()
            ->addStoreFilter($store->getStoreId())
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('nokaut_category_id')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('short_description')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('status')
            ->addAttributeToFilter('nokaut_category_id', array(
                'notnull' => true,
                'neq' => ''
            ));
        foreach ($additional_attributes as $code => $options) {
            $product_collection->addAttributeToSelect($code);
        }
        $product_collection = $this->addMediaGalleryAttributeToCollection($product_collection);
        $offers = array();
        $_category = Mage::getModel('nokaut/category');
        $images_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product';
        foreach ($product_collection as $product) {
            if ($product->isVisibleInSiteVisibility() && $product->isVisibleInCatalog()) {
                $attrs = array();
                foreach ($conditions as $attr => $data) {
                    if (array_key_exists('code', $data)) {
                        if (!empty($data['code']) && $product->getData($data['code']) !== null) {
                            $options = $additional_attributes[$data['code']];
                            if (empty($options)) {
                                $attrs[$attr] = (int) ($product->getData($data['code']) == $data['value']);
                            } else {
                                $option = array_search($product->getData($data['code']), $options);
                                $attrs[$attr] = (int) ($option == $data['value']);
                            }
                        }
                    } else if (array_key_exists('values', $data)) {
                        if (is_array($data['values'])) {
                            foreach ($data['values'] as $value => $value_data) {
                                if (!empty($value_data['code'])) {
                                    $options = $additional_attributes[$value_data['code']];
                                    if (empty($options)) {
                                        if ($product->getData($value_data['code']) == $value_data['value']) {
                                            $attrs[$attr] = $value;
                                            break;
                                        }
                                    } else {
                                        if ($product->getData($value_data['code'])) {
                                            $option = $options[$product->getData($value_data['code'])];
                                            if ($option == $value_data['value']) {
                                                $core_attrs[$attr] = $value;
                                                break;
                                            }
                                        }
                                    }   
                                }
                            }
                        }
                        if (!isset($attrs[$attr]) && isset($data['default'])) {
                            $attrs[$attr] = $data['default'];
                        }
                    }
                }
				foreach ($mappings['mappings'] as $attr => $mapping) {
                    if (!empty($mapping)) {
                        $value = $product->getData($mapping);
                        if (!empty($value)) {
                            $options = $additional_attributes[$mapping];
                            if (!empty($options)) {
                                $attrs[$attr] = $options[$value];
                            } else {
                                $attrs[$attr] = $value;
                            }
                        }
                    }
                }
                $image = null;
                $media_gallery = $product->getMediaGallery();
                $images = (isset($media_gallery['images'])) ? $media_gallery['images'] : array();
                if (count($images) > 0) {
                    $img = array_pop($images);
                    $image = $images_url . $img['file'];
                }
                $category = $_category->load($product->getNokautCategoryId())->getName();
                $price = $this->getFinalPriceIncludingTax($product);
                $offers[] = array(
                    'id' => $product->getSku(),
                    'url' => $product->getProductUrl(),
                    'price' => $price,
                    'name' => $product->getName(),
                    'description' => $product->getDescription() ? $product->getDescription() : $product->getShortDescription(),
                    'image' => $image,
                    'category' => $category,
                    'attrs' => $attrs
                );
            }
        }
        return $offers;
    }
    
    public function addMediaGalleryAttributeToCollection($_productCollection) {
		$all_ids = $_productCollection->getAllIds();
		if (!empty($all_ids)) {
			$_mediaGalleryAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'media_gallery')->getAttributeId();
			$_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');

			$_mediaGalleryData = $_read->fetchAll('
				SELECT
					main.entity_id, `main`.`value_id`, `main`.`value` AS `file`,
					`value`.`label`, `value`.`position`, `value`.`disabled`, `default_value`.`label` AS `label_default`,
					`default_value`.`position` AS `position_default`,
					`default_value`.`disabled` AS `disabled_default`
				FROM `' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery') . '` AS `main`
					LEFT JOIN `' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery_value') . '` AS `value`
						ON main.value_id=value.value_id AND value.store_id=' . Mage::app()->getStore()->getId() . '
					LEFT JOIN `' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery_value') . '` AS `default_value`
						ON main.value_id=default_value.value_id AND default_value.store_id=0
				WHERE (
					main.attribute_id = ' . $_read->quote($_mediaGalleryAttributeId) . ') 
					AND (main.entity_id IN (' . $_read->quote($all_ids) . '))
				ORDER BY IF(value.position IS NULL, default_value.position, value.position) ASC    
			');


			$_mediaGalleryByProductId = array();
			foreach ($_mediaGalleryData as $_galleryImage) {
				$k = $_galleryImage['entity_id'];
				unset($_galleryImage['entity_id']);
				if (!isset($_mediaGalleryByProductId[$k])) {
					$_mediaGalleryByProductId[$k] = array();
				}
				$_mediaGalleryByProductId[$k][] = $_galleryImage;
			}
			unset($_mediaGalleryData);
			foreach ($_productCollection as &$_product) {
				$_productId = $_product->getData('entity_id');
				if (isset($_mediaGalleryByProductId[$_productId])) {
					$_product->setData('media_gallery', array('images' => $_mediaGalleryByProductId[$_productId]));
				}
			}
			unset($_mediaGalleryByProductId);
		}
        
        return $_productCollection;
    } 
    
    public function getIdsByCategoryIds($category_ids = array()) {
        $ids = array();
        $_category = Mage::getModel('catalog/category');
        foreach ($category_ids as $category_id) {
            $_category->load($category_id);
            if ($_category->getId()) {
                foreach ($_category->getProductCollection() as $product) {
                    $id = $product->getId();
                    if (!isset($ids[$id])) {
                        $ids[$id] = $id;
                    }
                }
            }
        }
        return $ids;
    }
    
    public function updateNokautCategory($product_ids = array(), $nokaut_category_id) {
        $error = false;
        try {
            foreach ($product_ids as $id) {
                $this->unsetData()->load($id);
                if ($this->getId() && $this->getNokautCategoryId() != $nokaut_category_id) {
                    $this->setNokautCategoryId($nokaut_category_id);
                    $this->getResource()->saveAttribute($this, 'nokaut_category_id');
                }
            }
        } catch (Exception $e) {
            $error = true;
            Mage::log($e->getMessage(), null, 'nokaut.log');
        }
        return !$error;
    }
    
    public function getFinalPriceIncludingTax($product) {
        return Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), 2);
    }
    
}