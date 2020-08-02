<?php
namespace Lonicera;

use Lonicera\core\Route;

class Lonicera
{
    private $route;

    public function run()
    {
        require_once _SYS_PATH.'core/Loader.php';
        spl_autoload_register(['Lonicera\core\Loader', 'loadLibClass']);

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
        $group = $this->route->group;
        $className = "app\\{$group}\module\controller\\{$controlName}";
        // $path = _APP.$this->route->group.DIRECTORY_SEPARATOR.'module';
        // $path .= DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$controlName.'.php';
        // require_once $path;
        $methods = get_class_methods($className);
        if (!in_array($actionName, $methods, TRUE)) {
            throw new \Exception(sprintf('方法名 %s -> %s 不存在或非public', $controlName, $actionName));
        }
        $handler = new $className(); // 实例控制器
        $reflectedClass = new \ReflectionClass('Lonicera\core\Controller');
        $reflectedProperty = $reflectedClass->getProperty('route');
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($this->route);
        $handler->{$actionName}();
    }
}