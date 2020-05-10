<?php

function debug() {
    $numargs = func_num_args();
    $arg_list = func_get_args();

    for ($i=0; $i < $numargs; $i++) { 
        echo "第{$i}个变量的值为：{$arg_list[$i]}".PHP_EOL;
    }
    echo "当前所处的文件名为：".__FILE__.PHP_EOL;
}

/**
 * 在递归中使用debug函数
 */
function factor1($n) {
    $factor = 1;
    for ($i=1; $i <= $n; $i++) { 
        $factor *= $i;
        debug($factor, $i);
    }
    return $factor;
}

$result = factor1(4);