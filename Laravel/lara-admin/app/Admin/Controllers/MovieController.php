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
        $grid->column('title');

        // 第三列显示director字段，通过display($callback)方法设置这一列的显示内容为users表中对应的用户名
        $grid->column('director')->display(function($userid) {
            return User::find($userid)->name;
        });

        // 第四列显示为describe字段
        $grid->column('describe');

        return $content
            // ->title($this->title())
            ->header('用户管理首页')
            ->description('可以进行用户的增删改查的工作')
            ->body($this->grid()); // body方法添加内容
    }
}
