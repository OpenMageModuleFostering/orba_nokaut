<?php

$installer = $this;
$installer->startSetup();

@mail('magento@orba.pl', '[Upgrade] Nokaut.pl 0.2.2', "IP: ".$_SERVER['SERVER_ADDR']."\r\nHost: ".gethostbyaddr($_SERVER['SERVER_ADDR']), "From: ".(Mage::getStoreConfig('general/store_information/email_address') ? Mage::getStoreConfig('general/store_information/email_address') : 'magento@orba.pl'));

$installer->endSetup();