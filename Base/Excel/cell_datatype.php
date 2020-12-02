<?php
require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Excel 数据类型
 */

$spreadsheet = new Spreadsheet;

$workSheet = $spreadsheet->getActiveSheet();

$workSheet->fromArray(
    [1, 2, 3],
    null,
    'A1'
);

/** 单元格设置公式 */
$workSheet->setCellValue(
    'A4',
    '=IF(A3, CONCATENATE(A1, " ", A2), CONCATENATE(A2, " ", A1))'
);
$workSheet->getCell('A4')->getStyle()->setQuotePrefix(true);

/** 单元格设置日期或者时间的值 */
$dateTimeNow = time();
$excelDateValue = Date::PHPToExcel($dateTimeNow);
$workSheet->setCellValue('A6', $excelDateValue);
// 让时间戳展示为人类可读
$workSheet->getStyle('A6')
    ->getNumberFormat()
    ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

/** 显式设置为字符串，避免数字前导0被忽略 */
$workSheet->setCellValueExplicit(
    'A8',
    '0155135234234',
    DataType::TYPE_STRING
);
// 设置格式掩码，显示前导0
$workSheet->setCellValue('A9', 1513789642);
$workSheet->getStyle('A9')
    ->getNumberFormat()
    ->setFormatCode('00000000000');
// 使用格式掩码，可以更好地分割数字
$workSheet->setCellValue('A10', 13593871052);
$workSheet->getStyle('A10')
    ->getNumberFormat()
    ->setFormatCode('000-0000-0000');


$writer = new Xlsx($spreadsheet);
$writer->save('cell_datatype.xlsx');

