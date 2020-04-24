<?php
session_start();
define("PE_VERSION",'6.0');
define("PEPATH",dirname(__FILE__));

if (file_exists(PEPATH."/debug.php"))
    require PEPATH."/debug.php";

require PEPATH."/lib/init.cls.php";
$ginkgo = new ginkgo;
$ginkgo->run();