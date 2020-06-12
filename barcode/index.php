<?php
require __DIR__ . '/vendor/autoload.php';

use BarcodeBakery\Common\BCGColor;
use BarcodeBakery\Common\BCGDrawing;
use BarcodeBakery\Common\BCGFontFile;
use BarcodeBakery\Common\BCGLabel;

use BarcodeBakery\Barcode\BCGcode128;

// The arguments are R, G, and B for color.
$colorFront = new BCGColor(0, 0, 0);
$colorBack = new BCGColor(255, 255, 255);
$font = new BCGFontFile(__DIR__ . '/font/Arial.ttf', 18);

$code = new BCGcode128();
$code->setScale(2); // Resolution
$code->setThickness(30); // Thickness
$code->setForegroundColor($colorFront); // Color of bars
$code->setBackgroundColor($colorBack); // Color of spaces
$code->setFont($font); // Font (or 0)
$code->parse('1234'); // Text
$drawing = new BCGDrawing('hello.png', $colorBack);
$drawing->setBarcode($code);
$drawing->draw();
$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);