<?php
$classFile = 'BCGi25.php';
$className = 'BCGi25';
$baseClassFile = 'BCGBarcode1D.php';
$codeVersion = '6.0.0';

function customSetup($barcode, $get)
{
    if (isset($get['checksum'])) {
        $barcode->setChecksum($get['checksum'] === '1' ? true : false);
    }
}
