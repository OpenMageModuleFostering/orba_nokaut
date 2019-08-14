<?php
$installer = $this;

$installer->startSetup();

$this->sendPing('1.0.0', true);

$installer->endSetup();