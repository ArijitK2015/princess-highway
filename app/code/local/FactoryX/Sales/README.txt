Factory X - Sales Extension

Compatibility : Magento 1.6 tested / Magento 1.5 to test
				magento 1.4 => please use the other version of the extension

Functionalities:
- PDF Attachment to the order email
- Order status automatic changes
- History tab sorted by date

Installation:
- copy the Sales folder to app/code/local/FactoryX
- copy your PDF attachment file wherever you want in app/locale and then change the app/code/local/FactoryX/Sales/etc/config.xml and replace the folder and filename value.
- copy the FactoryX_Sales.xml file to app/etc/modules 
- create the following status in the backend :
	processing_part_shipped_nt / Processing - Partially Shipped No Tracking
	processing_shipped_nt / Processing - Shipped No Tracking
	processing_part_shipped / Processing - Partially Shipped
	processing_stage2 / Processing - Stage 2
- assign all of them to the state processing
- flush the cache and you're ready to go

Extra: you can run the shipped_orders.php script to change the status of your old orders.


[root@webdev2 mage]# find . -name History.php
./app/code/local/FactoryX/Audit/Block/Sales/Order/View/Tab/History.php
./app/code/core/Mage/Dataflow/Model/Resource/Profile/History.php
./app/code/core/Mage/Dataflow/Model/Mysql4/Profile/History.php
./app/code/core/Mage/Dataflow/Model/Profile/History.php
./app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/History.php
./app/code/core/Mage/Adminhtml/Block/Sales/Order/View/History.php
./app/code/core/Mage/Adminhtml/Block/System/Convert/Profile/Edit/Tab/History.php
./app/code/core/Mage/Sales/Model/Entity/Order/Status/History.php
./app/code/core/Mage/Sales/Model/Resource/Order/Status/History.php
./app/code/core/Mage/Sales/Model/Mysql4/Order/Status/History.php
./app/code/core/Mage/Sales/Model/Order/Status/History.php
./app/code/core/Mage/Sales/Block/Order/History.php
./app/code/core/Mage/XmlConnect/Model/Resource/History.php
./app/code/core/Mage/XmlConnect/Model/Mysql4/History.php
./app/code/core/Mage/XmlConnect/Model/History.php
./app/code/core/Mage/XmlConnect/Block/Adminhtml/Mobile/Edit/Tab/Submission/History.php
./app/code/core/Mage/XmlConnect/Block/Adminhtml/History.php
== Email Templates ==

=== Update Layout ===

System -> Configuration -> Design -> Themes -> Templates = gorman

add to layout factoryx/sales.xml
<?xml version="1.0"?>
<layout version="0.1.0">
    <sales_email_order_shipment_items_not_shipped>
        <block type="sales/order_email_shipment_items" name="items" template="email/order/shipment/items_not_shipped.phtml">
            <action method="addItemRender"><type>default</type><block>sales/order_email_items_default</block><template>email/order/items/shipment/not_shipped.phtml</template></action>
        </block>
        <block type="core/text_list" name="additional.product.info" />
    </sales_email_order_shipment_items_not_shipped>
</layout>

=== Update Transaction Email ===

{{layout handle="sales_email_order_shipment_items_not_shipped" shipment=$shipment order=$order}}

