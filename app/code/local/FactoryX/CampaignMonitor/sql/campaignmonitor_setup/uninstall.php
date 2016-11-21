<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

if ($conn->tableColumnExists($subscriberTable, 'subscriber_firstname'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_firstname');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_lastname'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_lastname');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_mobile'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_mobile');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_dob'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_dob');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_state'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_state');
}

if ($conn->tableColumnExists($subscriberTable, 'source'))
{
    $conn->dropColumn($subscriberTable, 'source');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_periodicity'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_periodicity');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_jobinterest'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_jobinterest');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_subscriptiondate'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_subscriptiondate');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_postcode'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_postcode');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_coupon'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_coupon');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_securehash'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_securehash');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_state'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_state');
}

if ($conn->tableColumnExists($subscriberTable, 'subscriber_preferredstore'))
{
    $conn->dropColumn($subscriberTable, 'subscriber_preferredstore');
}

$installer->endSetup();
