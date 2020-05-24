<?php
$a = 1;
xdebug_debug_zval('a');

$b = &$a;
xdebug_debug_zval('a');

$b += 5;
xdebug_debug_zval('a');