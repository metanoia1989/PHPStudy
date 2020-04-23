<?php

/*
 * Created on 2013-12-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
if(php_sapi_name() != 'cli')exit('Access denied!');
set_time_limit(0);
define('PEPATH',dirname(dirname(__FILE__)));
class app
{
	public $G;

	public function __construct(&$G)
	{
		$this->G = $G;
		$this->ev = $this->G->make('ev');
	}

	public function run()
	{
		phpinfo();
	}
}

include PEPATH.'/lib/init.cls.php';
$app = new app(new ginkgo);
$app->run();


?>