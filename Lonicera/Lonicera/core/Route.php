<?php
namespace Lonicera\core;

class Route 
{
    public $group;  // 分组名，或称为 module
    public $control;  // 控制器
    public $action; // 控制器中的方法
    public $params; // 传给 action 的参数

    public function __construct()
    {

    }

    public function init()
    {
        $route = $this->getRequest(); 
        $this->group = $route['group'];
        $this->control = $route['controll'];
        $this->action = $route['action'];
        !empty($route['param']) && $this->params = $route['param'];
    }

    /**
     * 在这里可以对不同样式的URL进行分门别类的处理，
     * 对Path Url方式和传统URL方式的解析
     * 以及对PATH_INFO模式的URL解析
     *
     * @return array
     */
    public function getRequest()
    {
        $filter_param = ['<', '>', '"', "'", '%3C', '%3E', '%22', '%27', '%3c', '%3e'];
        $uri = str_replace($filter_param, '', $_SERVER['REQUEST_URI']);
        $path = parse_url($uri);
        if (strpos($path['path'], 'index.php') === false) {
            $urlR0 = $path['path']; // 处理urlrewrite后的URL，此时不带index.php
        } else {
            $urlR0 = substr($path['path'], strlen('index.php') + 1);
        }
        $urlR = ltrim($urlR0, '/'); // 移除左边的/，得到最需要的URL
        if ($urlR == '') { // 传统的URL
            $route = $this->parseTradition();
            return $route;
        }

        // PATH_INFO 模式的URL解析
        $regArr = explode('/', $urlR);
        // 现在去除空白字符，index.php/g // b, 多个斜杠产生的空数组
        $regArr = array_filter($regArr, function ($item) {
            return !empty($item);
        });
        $cnt = count($regArr);
        if (empty($regArr) || empty($regArr[0])) {
            $cnt = 0;
        }
        switch ($cnt) {
            case 0:
                $route['group'] = $GLOBALS['_config']['defaultApp'];
                $route['controll'] = $GLOBALS['_config']['defaultController'];
                $route['action'] = $GLOBALS['_config']['defaultAction'];
                break;
            case 1:
                if (stripos($regArr[0], ':')) {
                    $gc = explode(':', $regArr[0]);
                    $route['group'] = $gc[0];
                    $route['controll'] = $gc[1];
                    $route['action'] = $GLOBALS['_config']['defaultAction'];
                } else {
                    $route['group'] = $GLOBALS['_config']['defaultApp'];
                    $route['controll'] = $regArr[0];
                    $route['action'] = $GLOBALS['_config']['defaultAction'];
                }
                break;
            default:
                if (stripos($regArr[0], ':')) {
                    $gc = explode(':', $regArr[0]);
                    $route['group'] = $gc[0];
                    $route['controll'] = $gc[1];
                    $route['action'] = $regArr[1];
                } else {
                    $route['group'] = $GLOBALS['_config']['defaultApp'];
                    $route['controll'] = $regArr[0];
                    $route['action'] = $regArr[1];
                }
                for ($i = 2; $i < $cnt; $i++) {
                    $route['param'][$regArr[$i]] = isset($regArr[++$i]) ? $regArr[$i] : '';
                }
                break;
        }

        // 需要处理 query 字符串了
        if (!empty($path['query'])) {
            parse_str($path['query'], $routeQ);
            if (empty($route['param'])) {
                $route['param'] = [];
            }
            $route['param'] += $routeQ;
        }

        // 注意，需要初始化param数组
        return $route;
    }

    /**
     * 解析传统形式的URL
     *
     * @return array
     */
    public function parseTradition()
    {
        $route = [];
        if (!isset($_GET[$GLOBALS['_config']['UrlGroupName']])) {
            $_GET[$GLOBALS['_config']['UrlGroupName']] = '';
        }
        if (!isset($_GET[$GLOBALS['_config']['UrlControllerName']])) {
            $_GET[$GLOBALS['_config']['UrlControllerName']] = '';
        }
        if (!isset($_GET[$GLOBALS['_config']['UrlActionName']])) {
            $_GET[$GLOBALS['_config']['UrlActionName']] = '';
        }
        $route['group']  = $_GET[$GLOBALS['_config']['UrlGroupName']];
        $route['controll']  = $_GET[$GLOBALS['_config']['UrlControllerName']];
        $route['action']  = $_GET[$GLOBALS['_config']['UrlActionName']];
        unset($_GET[$GLOBALS['_config']['UrlGroupName']]);
        unset($_GET[$GLOBALS['_config']['UrlControllerName']]);
        unset($_GET[$GLOBALS['_config']['UrlActionName']]);
        $route['param'] = $_GET;  // 如果要获得最原始的数据，可以使用$_REQUEST
        if ($route['group'] == NULL) {
            $route['group'] = $GLOBALS['_config']['defaultApp'];
        }
        if ($route['controll'] == NULL) {
            $route['controll'] = $GLOBALS['_config']['defaultController'];
        }
        if ($route['action'] == NULL) {
            $route['action'] = $GLOBALS['_config']['defaultAction'];
        }
        return $route;
    }

}