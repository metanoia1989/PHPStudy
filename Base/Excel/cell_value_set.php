<?php
require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Excel 单元格数值设置
 */

$spreadsheet = new Spreadsheet;

$workSheet = $spreadsheet->getActiveSheet();

/** 从数组来设置范围数据 */
// 二维数组多行多列
$arrayData = [
    [NULL, 2010, 2011, 2012],
    ['Q1', 12, 15, 21],
    ['Q2', 56, 73, 86],
    ['Q3', 52, 61, 69],
    ['Q4', 30, 32, 0],
];
$workSheet->fromArray($arrayData, NULL, 'C3'); // 最后一个参数为范围的左上角的值    

// 一维数组就是一行
$rowArray = ['Value1', 'Value2', 'Value3', 'Value4'];
$workSheet->fromArray($rowArray, NULL, 'C9');

// 二维数组，每个数组只有一个值，就是一列
$columnArray = array_chunk($rowArray, 1);
$workSheet->fromArray($columnArray, NULL, 'C11');


/** 根据单元格坐标处理值 */
// 根据坐标获取值
$cellValue = $workSheet->getCell('A1')->getValue(); // 未格式化的值
$cellValue = $workSheet->getCell('A1')->getCalculatedValue(); // 公式计算的值
$cellValue = $workSheet->getCell('A1')->getFormattedValue(); // 人类可读的值 已格式化

// 设置值通过行、列数
$workSheet->setCellValueByColumnAndRow(1, 5, 'PhpSpreadsheet');
$cellValue = $workSheet->getCellByColumnAndRow(1, 5)->getValue();
echo "第1行第5列的值为：".$cellValue.PHP_EOL;


// 获取指定范围的数值
$dataArray = $workSheet->rangeToArray(
    'C3:E5',
    NULL, // 会被过滤的值
    TRUE, // 是否计算公式的值
    TRUE, // 是否格式化值
    TRUE, // 数组是否应该按单元格行和单元格列索引
);
print_r($dataArray);

// 这个函数暂时还不知道怎么用
// $dataArray = $workSheet->namedRangeToArray(
//     'C3:E5',
//     NULL, // 会被过滤的值
//     TRUE, // 是否计算公式的值
//     TRUE, // 是否格式化值
//     TRUE, // 数组是否应该按单元格行和单元格列索引
// );
// print_r($dataArray);

/** Value binders 值绑定简化输入 */
Cell::setValueBinder(new AdvancedValueBinder);
$workSheet->setCellValue('A20', 'Percentage value:');
$workSheet->setCellValue('B20', '10%');
$workSheet->setCellValue('A21', 'Date/time value:');
$workSheet->setCellValue('B21', '21 December 1983');




$writer = new Xlsx($spreadsheet);
$writer->save('cell_value.xlsx');