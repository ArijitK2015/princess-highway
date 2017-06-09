<?php
$states = implode("','", Mage::helper('careers')->getStates());
$worktypes = implode("','", Mage::helper('careers')->getWorkTypes());
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
$careersTable = $this->getTable('careers/careers');

if ($conn->tableColumnExists($careersTable, 'countrys')) {
    $conn->changeColumn($careersTable, 'countrys', 'area', "ENUM('$states') NOT NULL DEFAULT 'VIC'");
}
else
{
    $isAreaEnum = Mage::helper('careers')->isColumnEnum($conn,$careersTable,'area');
    if (!$isAreaEnum) {
        $conn->modifyColumn($careersTable, 'area', "ENUM('$states') NOT NULL DEFAULT 'VIC'");
    }
}

$isWorkTypeEnum = Mage::helper('careers')->isColumnEnum($conn,$careersTable,'work_type');
if (!$isWorkTypeEnum) {
    $conn->modifyColumn($careersTable, 'work_type', "ENUM('$worktypes') NOT NULL DEFAULT 'Part Time'");
}

$installer->endSetup();
