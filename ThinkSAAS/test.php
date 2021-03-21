<?php
define("IN_TS", true);


define('THINKROOT', dirname(__FILE__));
define('THINKAPP', THINKROOT . '/app');
define('THINKDATA', THINKROOT . '/data');
define('THINKSAAS', THINKROOT . '/thinksaas');
define('THINKINSTALL', THINKROOT . '/install');
define('THINKPLUGIN', THINKROOT . '/plugins');

//系统Url参数变量
$TS_URL = array(
    'app'=>isset($_GET['app']) ? tsUrlCheck($_GET['app']) : 'home',//APP专用
    'ac'=>isset($_GET['ac']) ? tsUrlCheck($_GET['ac']) : 'index',//Action专用
    'ts'=>isset($_GET['ts']) ? tsUrlCheck($_GET['ts']) : '',//ThinkSAAS专用
    'mg'=>isset($_GET['mg']) ? tsUrlCheck($_GET['mg']) : 'index',//Admin管理专用
    'my'=>isset($_GET['my']) ? tsUrlCheck($_GET['my']) : 'index',//我的社区专用
    'api'=>isset($_GET['api']) ? tsUrlCheck($_GET['api']) : 'index',//Api专用
    'plugin'=>isset($_GET['plugin']) ? tsUrlCheck($_GET['plugin']) : '',//plugin专用
    'in'=>isset($_GET['in']) ? tsUrlCheck($_GET['in']) : '',//plugin专用
    'tp'=>isset($_GET['tp']) ? tsUrlCheck($_GET['tp']) : '1',//tp 内容分页
    'page'=>isset($_GET['page']) ? tsUrlCheck($_GET['page']) : '1',//page 列表分页
);

//核心配置文件 $TS_CF 系统配置变量
$TS_CF = include THINKROOT . '/thinksaas/config.php';



//数据库配置文件
include THINKROOT.'/data/config.inc.php';
//加载APP配置文件
include THINKROOT.'/app/' . $TS_URL['app'] . '/config.php';
//连接数据库
include THINKSAAS.'/sql/' . $TS_DB['sql'] . '.php';
$db = new MySql($TS_DB);

require_once THINKROOT."/thinksaas/tsApp.php";
require_once THINKROOT."/thinksaas/tsFunction.php";

//MySQL数据库缓存
include 'thinksaas/tsMySqlCache.php';
$tsMySqlCache = new tsMySqlCache($db);

//加载网站配置文件
$TS_SITE = fileRead('data/system_options.php');
if ($TS_SITE == '') {
    $TS_SITE = $tsMySqlCache -> get('system_options');
}

//定义网站URL
define('SITE_URL', $TS_SITE['site_url']);



/**
 * 输出json数据响应
 *
 * @param array $data
 * @return void
 */
function output_json($data) {
    $output = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    header("Content-Type: application/json");
    exit($output);
}

/**
 * 测试 post 请求
 *
 * @return void
 */
function test_https_post()
{
    https_post("https://httpbin.org/post", ["hello"=> "world"]);
}

/**
 * 测试登陆，把这几行代码加到 app\home\action\index.php 上即可实现模拟登陆  
 *
 * @return void
 */
function test_login()
{
    $userid = 20323;
    $userData = aac("user")->getOneUser($userid);
    $_SESSION['tsuser']    = $userData;
    $GLOBALS['TS_USER'] = $userData;
    echo json_encode($GLOBALS['TS_USER']);
}

// test_https_post(); 
// test_login();