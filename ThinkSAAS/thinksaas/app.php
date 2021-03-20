<?php
/*
 * ThinkSAAS APP入口
 * @copyright (c) 2010-3000 ThinkSAAS All Rights Reserved
 * @code by QiuJun
 * @Email:thinksaas@qq.com
 */
defined('IN_TS') or die('Access Denied.');


if (is_file('app/' . $TS_URL['app'] . '/action/' . $TS_URL['ac'] . '.php')) {
	//开始执行APP action
	if (is_file('app/' . $TS_URL['app'] . '/action/common.php'))
		include 'app/' . $TS_URL['app'] . '/action/common.php';

	include 'app/' . $TS_URL['app'] . '/action/' . $TS_URL['ac'] . '.php';

} else {
	ts404();
}
