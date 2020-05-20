<?php

namespace App\Admin\Controllers;

use App\Movie;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MovieController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Movie';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Movie());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('director', __('Director'));
        $grid->column('describe', __('Describe'));
        $grid->column('rate', __('Rate'));
        $grid->column('released', __('Released'));
        $grid->column('release_at', __('Release at'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Movie::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('director', __('Director'));
        $show->field('describe', __('Describe'));
        $show->field('rate', __('Rate'));
        $show->field('released', __('Released'));
        $show->field('release_at', __('Release at'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Movie());

        $form->text('title', __('Title'));
        $form->number('director', __('Director'));
        $form->text('describe', __('Describe'));
        $form->switch('rate', __('Rate'));
        $form->text('released', __('Released'));
        $form->datetime('release_at', __('Release at'))->default(date('Y-m-d H:i:s'));

        return $form;
    }


    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content  这个类实现内容区的布局
     */
    public function index(Content $content)
    {
        $grid = new Grid(new Movie);

        // 第一列显示id字段，并将这一列设置为可排序的
        $grid->column('id', 'ID')->sortable();

        // 第二列显示title字段，由于title字段名和Grid对象的title方法冲突，所以用Grid的column()方法代替
        // setAttributes() 设置HTML属性
        $grid->column('title', "电影名称")->setAttributes(['style' => 'color: red']);

        // 第三列显示director字段，通过display($callback)方法设置这一列的显示内容为users表中对应的用户名
        // style() 设置样式
        $grid->column('director', '导演')->display(function($userid) {
            return User::find($userid)->name;
        })->style('font-weight: bold;');

        // 第四列显示为describe字段
        $grid->column('describe', '电影简介')->customPopover('left');

        // 第五列显示未rate字段
        $grid->column('rate', '电影评分');

        // 第六列显示 released 字段，通过display($callback) 方法来格式化显示输出
        $grid->column('released', '上映')->display(function ($released) {
            return $released ? '已上映' : '未上映';
        });

        $grid->column('no_in_table', '不存在的字段')->display(function () {
            return '这个字段并不存在';
        });

        $grid->column('no_in_table2', '呼和浩特')->display(function () {
            return '这个字段并不存在，呜呜呜呜呜';
        });
        $grid->column('no_in_table3', '大中至正')->display(function () {
            return '这个字段并不存在，JIngasfdsdf';
        });
        // hide() 默认会被隐藏，可以在右上角的列选择器开启显示
        $grid->column('no_in_table4', '思思是是是')->display(function () {
            return '这个字段并不存在，JIngasfdsdf';
        })->hide();

        // 如果当前里的输出数据为字符串，那么可以通过链式方法调用Illuminate\Support\Str的方法。
        $grid->column('string_handle', '字符串操作')->display(function () {
            return 'stringstringstringstring';
        })->limit(15)->ucfirst()->substr(1, 10);


        // 下面为三个时间段的列显示
        // help() 表头帮助信息
        $grid->column('release_at')->help('这是发布时间，呜啦啦啦啦');
        // color() 设置颜色
        $grid->column('created_at')->color('blue');
        // width() 设置列宽
        $grid->column('updated_at')->width(50)->customColor('green');

        // filter($callback) 方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            // 设置created_at字段的范围查询
            $filter->between('created_at', 'Created Time')->datetime();
        });

        $grid->model()->where('id', '<>', 1);

        // 限制每页显示条数
        $grid->paginate(10);

        // 禁用创建按钮
        $grid->disableCreateButton();
        // 禁用分页条
        // $grid->disablePagination();
        // 禁用查询过滤器
        $grid->disableFilter();
        // 禁用行选择checkbox
        $grid->disableRowSelector();
        // 禁用行操作列
        $grid->disableActions();
        // 禁用行选择器
        // $grid->disableColumnSelector();

        // 设置分页选择器选项
        $grid->perPages([5, 10, 15]);

        // fixColumns方法来设置固定列
        // 第一个参数表示固定从头开始的前三列，第二个参数表示固定从后往前数的两列
        $grid->fixColumns(3, -1);

        return $content
            // ->title($this->title())
            ->header('用户管理首页')
            ->description('可以进行用户的增删改查的工作')
            // ->body($this->grid()); // body方法添加内容
            ->body($grid); // body方法添加内容
    }
}
