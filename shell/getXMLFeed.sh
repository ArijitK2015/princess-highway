#!/bin/bash

minOutFileSize=1024
outFile=/var/www/magento/media/xmlfeed/catalog-lastest.xml

wget --tries=1 --user-agent="wget" \
 --output-document=$outFile \
 http://admin.shop.alannahhill.com.au/xmlfeed/index/catalog

outFileSize=$(wc -c <"$outFile")

# zero byte size check
# if [ -s $outFile ]; then

if [ $outFileSize -ge $minOutFileSize ]; then
	mv $outFile /var/www/magento/media/xmlfeed/catalog.xml
else
    # tell someone that cares
    # /usr/lib/sendmail
	mail -s "SLI feed failed size check!" ben.incani@factoryx.com.au < /dev/null
fi
