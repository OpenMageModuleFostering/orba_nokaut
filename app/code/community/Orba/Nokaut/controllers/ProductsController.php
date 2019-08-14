<?php
class Orba_Nokaut_ProductsController extends Mage_Core_Controller_Front_Action {
	
    protected function getConfig() {
        return Mage::getModel('nokaut/config');
    }
    
    public function feedAction() {
        $hash = $this->getRequest()->getParam('hash');
        if ($hash == $this->getConfig()->getHash()) {
            ini_set('max_execution_time', 0);
            header("Content-Type:text/xml");
            require_once(Mage::getBaseDir('lib').'/Nokaut/simple_xml_extended.php');
            $products = Mage::getModel('nokaut/product')->getOffers();
            $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE nokaut SYSTEM "http://www.nokaut.pl/integracja/nokaut.dtd"><nokaut />');
            $offers = $xml->addChild('offers');
            foreach ($products as $product) {
                $offer = $offers->addChild('offer');
                $offer->addChild('id')
                    ->addCData($product['id']);
                $offer->addChild('name')
                    ->addCData($product['name']);
                $offer->addChild('description')
                    ->addCData($product['description']);
                $offer->addChild('url')
                    ->addCData($product['url']);
                if (!empty($product['image'])) {
                    $offer->addChild('image')
                        ->addCData($product['image']);
                }
                $offer->addChild('price')
                    ->addCData($product['price']);
                $offer->addChild('category')
                    ->addCData($product['category']);
                foreach ($product['attrs'] as $attr => $value) {
                    $offer->addChild($attr)
                        ->addCData($value);
                }
            }
            echo $xml->asXML();
            die();
        } else {
            $this->_redirect('/');
        }
    }
    
}