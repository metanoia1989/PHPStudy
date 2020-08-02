<?php

/**
 * 做连接数据库和渲染视图的重复工作
 */
class Controller
{
    private $db;

    private $view;

    protected static $route;

    public function __construct()
    {
        require_once _ROOT.'library/render/PhpRender.php'; 
        $this->view = new PhpRender();
    }

    /**
     * 赋值给模板
     *
     * @param string $key
     * @param mixed $value
     * @return Render
     */
    protected function assign($key, $value)
    {
        $this->view->assign($key, $value);
        return $this->view;
    }

    /**
     * 调度DB
     *
     * @param array $conf
     * @return DB
     */
    public function db($conf = [])
    {
        if (is_null($conf) || empty($conf)) {
            $conf = $GLOBALS['_config']['db'];
        }
        $this->db = DB::getInstance($conf);
        return $this->db;
    }

    /**
     * 渲染视图并输出到浏览器
     *
     * @param string $file 视图名，默认为 group/control/action
     * @return mixed
     */
    public function display($file = "")
    {
        if (func_num_args() == 0 || $file == null) {
            $control = self::$route->control;
            $action = self::$route->action;
            $viewFilePath = _ROOT.'app/'.self::$route->group.'module/view/';
            $viewFilePath .= $control.DIRECTORY_SEPARATOR.$action.'.php';
        } else {
            $viewFilePath = $file . '.php';
        }
        $this->view->display($viewFilePath);
    }
}