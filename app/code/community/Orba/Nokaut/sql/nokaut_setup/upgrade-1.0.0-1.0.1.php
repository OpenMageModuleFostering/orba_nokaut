<?php
$installer = $this;

$installer->startSetup();

$this->sendPing('1.0.1', true);

$installer->endSetup();