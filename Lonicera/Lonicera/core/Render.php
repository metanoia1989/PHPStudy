<?php

/**
 * 模板引擎的接口
 */
interface Render
{
    public function init();
    public function assign($key, $value);
    public function display($view = '');
}