### feed url ###

http://shop.alannahhill.com.au/xmlfeed/index/catalog
http://shop.alannahhill.com.au/xmlfeed/index/catalog?limit=5

Note.
takes too long to run, so has been turned into an hourly cron job, sli will
request the file every 15 minutes

/etc/cron.hourly/sli-xmlfeed.cron

### sli-xmlfeed.cron ###

#!/bin/bash
wget --tries=1 --user-agent="wget" \
 --output-document=/home/shopa/www/media/xmlfeed/catalog-lastest.xml \
 http://shop.alannahhill.com.au/xmlfeed/index/catalog
mv /home/shopa/www/media/xmlfeed/catalog-lastest.xml /home/shopa/www/media/xmlfeed/catalog.xml

### example output ###
<product_list>
	<product>
		<name>Name of product</name>
		<url>http://www.yourdomain.com/productname.html%3C/url%3E;
		<sku>12345</sku>
		<short_desc>Short Description ...</short_desc>
		<long_desc>Long Description ...</long_desc>
		<image>http://www.yourdomain.com/images/products/yourproduct.jpg%3C/image%3E;
		<category>Womens</category>
		<sub_category>Sweaters</sub_category>
		<price>$29.99</price>
		<saleprice>$24.99</saleprice>
		<availability>1</availability>
		<mfn_num>98765</mfn_num>
		<brand>brand</brand>
		<colors>
			<color>Black</color>
			<color>White</color>
			<color>Brown</color>
		<colors>		
		<size>
		</size>
	</product>
</product_list>
