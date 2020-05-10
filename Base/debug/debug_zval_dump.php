<?php
// debug_zval_dump 记录一个变量被引用多少次，理解PHP写时复制、引用机制
/**
 * 在PHP文档中进行了描述，您必须通过引用传递参数。 但是在PHP 5.x中已删除了按引用传递参数。 
 * 这个问题导致我们无法使用debug_zval_dump。 使用此功能无法获得正确的引用计数。
 * 我猜不建议使用此功能来计数引用。 在我的测试环境（Apache / PHP7.1）中，您的示例生成的引用计数为1。似乎有些棘手。
 */
// 数值类型直接拷贝了，字符串、对象才有引用 =_=

$debugArray = [1, 2, 3];
foreach ($debugArray as $v) {
    $v *= 2;
    debug_zval_dump($v);
}
var_dump($debugArray);

$debugArray2 = ['23434', '2343', 3];
foreach ($debugArray2 as &$v) {
    $v = str_repeat($v, 2);
    debug_zval_dump($v);
}
var_dump($debugArray2);


$var1 = 'Hello World';
$var2 = '';

$var2 =& $var1;

debug_zval_dump($var1);