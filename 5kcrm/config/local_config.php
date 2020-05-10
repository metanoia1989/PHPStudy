<?php
//****************************************** 
// 本地开发配置，可以设置与服务器不同的配置
//****************************************** 
define('DEVELOPMENT', true); // 开发模式，本地模式


/**
 * 本地数据库配置
 *
 * @return array
 */
function localDbConfig()
{
    return [
        'type'            => 'mysql',
        // 服务器地址
        'hostname'        => 'localhost',
        // 数据库名
        'database'        => '5kcrm',
        // 用户名
        'username'        => 'root',
        // 密码
        'password'        => 'root',
    ];
}

/**
 * 本地应用配置
 *
 * @return array
 */
function localAppConfig()
{
    return [
        // 应用调试模式
        'app_debug'              => true,
        // 应用Trace
        'app_trace'              => true,
    ];
}