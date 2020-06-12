<?php
require __DIR__ . '/vendor/autoload.php';

use BarcodeBakery\Common\BCGColor;
use BarcodeBakery\Common\BCGDrawing;
use BarcodeBakery\Common\BCGFontFile;
use BarcodeBakery\Common\BCGLabel;

use BarcodeBakery\Barcode\BCGcode128;

// The arguments are R, G, and B for color.
// $colorFront = new BCGColor(0, 0, 0);
// $colorBack = new BCGColor(255, 255, 255);
// $font = new BCGFontFile(__DIR__ . '/font/Arial.ttf', 18);

$code = new BCGcode128();
$f = imagecreatefromstring(file_get_contents("page.png"));
$code->draw($f);
$code->parse("1234");
$drawing = new BCGDrawing('hello.png', $colorBack);
$drawing->setBarcode($code);
$drawing->draw();
$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);