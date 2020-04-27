<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('页面内容和布局') // 页面标题
            ->description('管理员后台...') // 描述小标题
            ->breadcrumb(   // 面包屑导航
                ['text' => '首页', 'url' => '/admin'],
                ['text' => '页面内容布局', 'url' => '/admin/page'],
                ['text' => '查看布局']
            )
            ->body('填充页面body部分，可以填入任何可被渲染的对象') // 页面body部分
            ->body('在body中添加另一段内容')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
