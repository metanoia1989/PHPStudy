<?php
require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


$spreadsheet = new Spreadsheet;

/* 设置表格内容的几种方式 */
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->setCellValue('A1', 'PhpSpreadsheet');
$spreadsheet->getActiveSheet()->setCellValue('A2', 12345.6789);
$spreadsheet->getActiveSheet()->setCellValue('A3', TRUE);
$spreadsheet->getActiveSheet()->setCellValue(
    'A4',
    '=IF(A3, CONCATENATE(A1, " ", A2), CONCATENATE(A2, " ", A1))'
);

$spreadsheet->getActiveSheet()
    ->getCell('B8')
    ->setValue('Some value');

$writer = new Xlsx($spreadsheet);
$writer->save('access_cell.xlsx');


/* 访问单元格内容的方法 */  
$spreadsheet_one = new Spreadsheet();
$workSheet = $spreadsheet->getActiveSheet();

$workSheet->fromArray(
    [1, 2, 3],
    null,
    'A1'
);
// 对getCell() （或任何类似方法）的调用将返回单元格数据以及指向该集合的指针。
$cellC1 = $workSheet->getCell('C1');
echo "Value: ".$cellC1->getValue()."; Address: ".$cellC1->getCoordinate().PHP_EOL;
$cellA1 = $workSheet->getCell('A1');
echo "Value: ".$cellA1->getValue()."; Address: ".$cellA1->getCoordinate().PHP_EOL;
// echo "Value: ".$cellC1->getValue()."; Address: ".$cellC1->getCoordinate().PHP_EOL; // 报错，因为指针为空了  
