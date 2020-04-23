<?php
/*
 * Created on 2013-12-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

define('PEPATH',dirname(dirname(__FILE__)));
class app
{
	public $G;

	public function __construct(&$G)
	{
		$this->G = $G;
		$this->ev = $this->G->make('ev');
		$this->sql = $this->G->make('pdosql');
		$this->db = $this->G->make('pepdo');
		$this->pg = $this->G->make('pg');
		$this->module = $this->G->make('module');
		$this->session = $this->G->make('session');
		$this->user = $this->G->make('user','user');
		$groups = $this->user->getUserGroups();
		$this->order = $this->G->make('orders','bank');
	}

	public function run()
	{
		//使用通用通知接口
		$notify = $this->G->make('wxpay')->getNotifyPubObject();
		$notify->handle(false);
		exit;
	}
}
include PEPATH.'/lib/init.cls.php';
$app = new app(new ginkgo);
$app->run();

?>