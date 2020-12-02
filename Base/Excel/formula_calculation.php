<?php
require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/** 公式计算实例 */
$spreadsheet = new Spreadsheet;

$worksheet = $spreadsheet->getActiveSheet();


$database = [
    [ 'Tree',  'Height', 'Age', 'Yield', 'Profit' ],
    [ 'Apple',  18,       20,    14,      105.00  ],
    [ 'Pear',   12,       12,    10,       96.00  ],
    [ 'Cherry', 13,       14,     9,      105.00  ],
    [ 'Apple',  14,       15,    10,       75.00  ],
    [ 'Pear',    9,        8,     8,       76.80  ],
    [ 'Apple',   8,        9,     6,       45.00  ],
];

$criteria = [
    [ 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ],
    [ '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ],
    [ '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ],
];

$worksheet->fromArray( $criteria, NULL, 'A1' );
$worksheet->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DAVERAGE(A4:E10,"Yield",A1:B2)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();


$writer = new Xlsx($spreadsheet);
$writer->save('formula_calculation.xlsx');