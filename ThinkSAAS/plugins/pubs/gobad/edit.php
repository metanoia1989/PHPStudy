<?php
defined('IN_TS') or die('Access Denied.');

//插件编辑
switch($ts){
	case "set":

		$strAbout = fileRead('plugins/pubs/gobad/about.php');

		$code = fileRead('data/plugins_pubs_gobad.php');
		
		if($code==''){
			
			$code = $tsMySqlCache->get('plugins_pubs_gobad');
			
		}

        include template('edit_set','gobad');
		break;
		
	case "do":
		$code = $_POST['code'];
		
		fileWrite('plugins_pubs_gobad.php','data',$code);
		$tsMySqlCache->set('plugins_pubs_gobad',$code);
		
		header('Location: '.SITE_URL.'index.php?app=pubs&ac=plugin&plugin=gobad&in=edit&ts=set');
		break;
}