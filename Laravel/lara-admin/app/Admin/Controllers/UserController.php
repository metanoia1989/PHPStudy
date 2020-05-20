<?php

namespace App\Admin\Controllers;

use App\AdminUser as User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('email_verified_at', __('Email verified at'));
        $grid->column('password', __('Password'));
        $grid->column('remember_token', __('Remember token'));
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->field('remember_token', __('Remember token'));
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
        $form = new Form(new User());

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        $form->password('password', __('Password'));
        $form->text('remember_token', __('Remember token'));

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
        return $content
            // ->title($this->title())
            ->header('用户管理首页')
            ->description('可以进行用户的增删改查的工作')
            ->body($this->grid()); // body方法添加内容
    }

    /**
     * 布局测试
     *
     * @param Content $content
     *
     * @return Content
     */
    public function layout(Content $content)
    {
        $content->title('布局测试')->description('栅格系统');

        // 返回一行内容
        $content->row('hello');

        // 一行内添加多列内容
        $content->row(function(Row $row) {
            $row->column(4, "金刚葫芦娃");
            $row->column(4, "无敌小旋风");
            $row->column(4, "超级赛亚人");
        });

        // 列中添加行
        $content->row(function(Row $row) {
            $row->column(4, "第一列文字：呼啦啦啦啦");
            $row->column(8, function(Column $column) {
                $column->row("第二列第一行：不善护身");
                $column->row("第二列第二行：不守根门");
                $column->row("第二列第三行：不摄其念");
            });
        });

        // 行内添加行，行内再添加列
        $content->row(function (Row $row) {
            $row->column(4, "第一列的文字：观察女人少壮好色而生染着");
            $row->column(8, function(Column $column) {
                $column->row("第二列第一行");
                $column->row("第二列第二行");
                $column->row(function (Row $row) {
                    $row->column(6, "第三列中的列");
                    $row->column(6, "第三列中的列");
                });
            });
        });

        return $content;
    }

    /**
     * 用户角色
     *
     * @return void
     */
    public function roles(Content $content)
    {
        $user = User::find(1);
        $roles = $user->roles;
        dd($roles);
        exit;
        $grid = new Grid(new User());

        $grid->column('id', 'ID')->sortable();
        $grid->column('username', '用户名');
        $grid->column('name', '真实姓名');

        $grid->column('roles')->display(function ($roles) {
            dd($roles);
            return 'ttt';
            // $roles = array_map(function ($role) {
            //     return "<span class='label label-success'>{$role['name']}</span>";
            // }, $roles);
            // return join('&nbsp', $roles);
        });

        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        return $content->body($grid);
    }
}
