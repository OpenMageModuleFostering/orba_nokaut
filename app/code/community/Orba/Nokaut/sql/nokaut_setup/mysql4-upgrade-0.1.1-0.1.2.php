<?php

$this->startSetup();

Mage::getModel('catalog/product')->getResource()->getAttribute('nokaut_category_id')
    ->setUsedInProductListing(true)
    ->save();

@mail('magento@orba.pl', '[Upgrade] Nokaut.pl 0.1.2', "IP: ".$_SERVER['SERVER_ADDR']."\r\nHost: ".gethostbyaddr($_SERVER['SERVER_ADDR']), "From: ".(Mage::getStoreConfig('general/store_information/email_address') ? Mage::getStoreConfig('general/store_information/email_address') : 'magento@orba.pl'));

$this->endSetup();