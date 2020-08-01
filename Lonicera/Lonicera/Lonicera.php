<?php

class Lonicera
{
    private $route;

    public function run()
    {
        require_once _SYS_PATH.'core/Route.php';

        $this->route();
        $this->dispatch();
    }

    /**
     * 初始化路由
     *
     * @return void
     */
    public function route()
    {
        $this->route = new Route();
        $this->route->init();
    }

    /**
     * 路由分发
     *
     * @return void
     */
    public function dispatch()
    {
        $controlName = $this->route->control.'Controller';
        $actionName = $this->route->action.'Action';
        $path = _APP.$this->route->group.DIRECTORY_SEPARATOR.'module';
        $path .= DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$controlName.'.php';
        require_once $path;

        $methods = get_class_methods($controlName);
        if (!in_array($actionName, $methods, TRUE)) {
            throw new Exception(sprintf('方法名 %s -> %s 不存在或非public', $controlName, $actionName));
        }
        $handler = new $controlName(); // 实例控制器
        $handler->param = $this->param;
        $handler->{$actionName}();
    }
}