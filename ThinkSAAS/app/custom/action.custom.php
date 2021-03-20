<?php
defined('IN_TS') or die('Access Denied.');

class customAction 
{
    /**
     * 首页
     *
     * @return void
     */
    public function index() 
    { 
        include template("index");
    }
}