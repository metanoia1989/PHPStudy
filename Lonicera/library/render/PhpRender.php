<?php
namespace library\render;

use Lonicera\core\Render;


class PhpRender implements Render
{
    // 赋值变量
    private $value = [];

    public function init()
    {

    }

    /**
     * 变量赋值
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function assign($key, $value) 
    {
        $this->value[$key] = $value;
    }

    /**
     * 视图显示
     *
     * @param string $view
     * @return void
     */
    public function display($view = '')
    {
        extract($this->value);
        include $view;
    }
}