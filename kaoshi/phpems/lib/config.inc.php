<?php

define('DOMAINTYPE','off');
define('CH','exam_');
define('CDO','');
define('CP','/');
define('CRT',180);
define('CS',md5(base64_encode($_SERVER['HTTP_HOST'])));
define('HE','utf-8');
define('PN',10);
define('TIME',time());
define('USEWX',false);//微信使用开关，绑定用户
define('WXAUTOREG',false);//微信开启自动注册
define('PAYJSASWX','YES');//使用PAYJZ的微信支付接口代替微信支付
define('OPENOSS',false);
if(dirname($_SERVER['SCRIPT_NAME']))
define('WP','http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/');
else
define('WP','http://'.$_SERVER['SERVER_NAME'].'/');

define('DB','phpems');//MYSQL数据库名
define('DH','127.0.0.1');//MYSQL主机名，不用改
define('DU','root');//MYSQL数据库用户名
define('DP','root');//MYSQL数据库用户密码
define('DTH','x2_');//系统表前缀，不用改

define('WXAPPID','wx1111132abf082e60');
define('WXAPPSECRET','3368f711111111110cee2c86341');
define('WXMCHID','1311111702');
define('WXKEY','zhelishi32weidewxkey');

define('ALIPART','2011111122450284825');
define('ALIKEY','j8tn111111x7l0wddmxyfytzt0kdkuaitkiw');
define('ALIACC','suo11111@126.com');
/**
**/
define('PAYJSMCHID','1551052561');
define('PAYJSKEY','Zz8ks1ZP3UPKeTGi');


?>