Files
=====
Z:\html\clearitonline\trunk\app\etc\modules\FactoryX_PickList.xml
Z:\html\clearitonline\trunk\app\design\adminhtml\default\default\layout\factoryx_picklist.xml
Z:\html\clearitonline\trunk\app\design\adminhtml\default\default\template\factoryx\page1.phtml
Z:\html\clearitonline\trunk\app\code\local\FactoryX\PickList\Helper\Data.php

app/code/local/FactoryX/PickList/*

Prerequists
===========
fpdf16
GNU barcode
genbarcode
php-barcode
FreeType - http://www.freetype.org/ (should be built with php see phpinfo)


1. fownload & install fpdf16.tgz
http://www.fpdf.org/?go=download

Extact to ./lib

2. download & install php-barcode-0.3pl1.tar.gz
http://www.ashberg.de/php-barcode/download/files/php-barcode-0.3pl1.tar.gz

Extact to ./lib

Note. Requires code edits to some global params.

3. download & install GNU barcode

$ cd /usr/local/src
$ wget http://ftp.gnu.org/gnu/barcode/barcode-0.98.tar.gz
$ tar xfvz barcode-0.98.tar.gz
$ cd barcode-0.98
$ ./configure
$ make
# make install
# ldconfig

4. download & install genbarcode-0.4.tar.gz

$ cd /usr/local/src
$ wget http://www.ashberg.de/php-barcode/download/files/genbarcode-0.4.tar.gz
$ tar xfvz genbarcode-0.4.tar.gz
$ cd genbarcode-0.4
$ make
$ make install


Trouble Shooting
================

1. problem - open_basedir restriction in effect
File(/usr/local/bin/genbarcode) is not within the allowed path(s)

solution
goto whm PHP open_basedir Tweak and Exclude Protection

2. problem - barcodes not appearing in pdf
Warning: popen() has been disabled for security reasons

solution
whm -> PHP Configuration Editor -> Safe Mode : disable_functions

symlink,shell_exec,exec,proc_close,proc_open,popen,system,dl,passthru,escapeshellarg,escapeshellcmd
symlink,shell_exec,exec,proc_close,proc_open,system,dl,passthru,escapeshellarg,escapeshellcmd

3. Cannot load the ionCube PHP Loader - it was built with configuration 2.2.0, whereas running engine is API220090626,NTS
Zend Optimizer requires Zend Engine API version 220060519.
The Zend Engine API version 220090626 which is installed, is newer.
Contact Zend Technologies at http://www.zend.com/ for a later version of Zend Optimizer.
