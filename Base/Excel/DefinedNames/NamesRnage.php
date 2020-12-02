<?php
require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// 设置一些基础的数据
$worksheet->setCellValue('A1', 'Tax Rate:')
    ->setCellValue('B1', '=19%')
    ->setCellValue('A3', 'Net Price:')
    ->setCellValue('B3', 12.99)
    ->setCellValue('A4', 'Tax:')
    ->setCellValue('A5', 'Price including Tax:');

// 定义命名范围 named range
$spreadsheet->addNamedRange(new NamedRange('TAX_RATE', $worksheet, '=$B$1'));
$spreadsheet->addNamedRange(new NamedRange('PRICE', $worksheet, '=$B$3'));

// 在公式中引用定义的命名范围
$worksheet->setCellValue('B4', '=PRICE*TAX_RATE')
    ->setCellValue('B5', '=PRICE*(1+TAX_RATE)');


$writer = new Xlsx($spreadsheet);
$writer->save('named_range.xlsx');