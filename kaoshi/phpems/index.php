<?php

session_start();
define("PE_VERSION",'6.0');
define("PEPATH",dirname(__FILE__));
require PEPATH."/lib/init.cls.php";
$ginkgo = new ginkgo;
$ginkgo->run();