<?php
$installer = $this;
$installer->startSetup();

Mage::getModel('nokaut/attribute')->addNokautAttributeToProduct();

$categoryTableName = $this->getTable('nokaut/category');
$categoryTable = $installer->getConnection()
    ->newTable($categoryTableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('external_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false
    ), 'External ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Name')
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'default' => 0
    ), 'Is Deleted');
$installer->getConnection()->createTable($categoryTable);

$mappingTableName = $this->getTable('nokaut/mapping');
$mappingTable = $installer->getConnection()
    ->newTable($mappingTableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('nokaut_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true
    ), 'Nokaut Category Internal ID')
    ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false
    ), 'Priority')
    ->addIndex($installer->getIdxName('nokaut/mapping', array('nokaut_category_id')),
        array('nokaut_category_id')
    )
    ->addForeignKey(
        $installer->getFkName('nokaut/mapping', 'nokaut_category_id', 'nokaut/category', 'id'),
        'nokaut_category_id', $installer->getTable('nokaut/category'), 'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($mappingTable);

$mappingCatalogCategoryTableName = $this->getTable('nokaut/mapping_catalog_category');
$mappingCatalogCategoryTable = $installer->getConnection()
    ->newTable($mappingCatalogCategoryTableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('mapping_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false
    ), 'Mapping ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false
    ), 'Magento category ID')
    ->addIndex($installer->getIdxName('nokaut/mapping_catalog_category', array('mapping_id')),
        array('mapping_id')
    )
    ->addIndex($installer->getIdxName('nokaut/mapping_catalog_category', array('category_id')),
        array('category_id')
    )
    ->addForeignKey(
        $installer->getFkName('nokaut/mapping_catalog_category', 'mapping_id', 'nokaut/mapping', 'id'),
        'mapping_id', $installer->getTable('nokaut/mapping'), 'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('nokaut/mapping_catalog_category', 'category_id', 'catalog/category', 'entity_id'),
        'category_id', $installer->getTable('catalog/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($mappingCatalogCategoryTable);

Mage::getModel('nokaut/config')->saveHash();

$this->sendPing('1.0.0');

$installer->endSetup();