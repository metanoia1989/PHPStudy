<?php

namespace App\Admin\Controllers;

use App\User;
use App\ColumnShow;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Collection;

class ShowController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));
        $grid->column('username', __('Username'));
        $grid->column('password', __('Password'));
        $grid->column('email', __('Email'));
        $grid->column('name', __('Name'));
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
        $show->field('username', __('Username'));
        $show->field('password', __('Password'));
        $show->field('email', __('Email'));
        $show->field('name', __('Name'));
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

        $form->text('username', __('Username'));
        $form->password('password', __('Password'));
        $form->email('email', __('Email'));
        $form->text('name', __('Name'));

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
        $grid = new Grid(new ColumnShow());

        // 根据条件显示不同的组件
        $grid->column('title')->display(function ($title, $column) {
            // 如果这一列的status字段的值等于1，直接显示title字段
            // 否则显示为editable
            return $this->status == 1 ? $title : $column->editable();
        });

        // display()方法来通过传入的回调函数来处理当前列的值
        $grid->column('title')->display(function ($title) {
            return "<span style='color:blue'>$title</span>";
        });
        // 在传入的匿名函数中可以通过任何方式对数据进行处理，另外匿名函数绑定了当前列的数据作为父对象，可以在函数中调用当前行的数据
        $grid->column('full_name')->display(function () {
            return $this->first_name . ' ' . $this->last_name;
        });
        // 要尽量避免在回调函数中去查询数据库或者调用外部接口，这样会在每一行渲染的时候运行查询或者外部接口的调用，严重影响性能，
        // 一个好的办法是给模型建立关联关系，然就用模型的with方法将关联数据一并查询出来

        // collection回调可以批量修改数据
        // $collection表示当前这一个表格数据的模型集合， 你可以根据你的需要来读取或者修改它的数据。
        $grid->model()->collection(function (Collection $collection) {
            // 1. 给每列加字段，类似 display
            foreach ($collection as $item) {
                $item->full_name = $item->first_name . ' ' . $item->last_name;
            }
            // 2. 给表格加一个序号列
            foreach ($collection as $index => $item) {
                $item->number = $index;
            }
            // 3. 从外部接口获取数据填充到模型集合中
            $ids = $collection->pluck('id');
            // $data = getDataFromApi($ids);
            // foreach ($collection as $index => $item) {
            //     $item->column_name = $data[$index];
            // }

            // 最后一个定要返回集合对象
            return $collection;
        });

        // 内容映射
        // 如果字段gender的取值为f、m，分别需要用女、男来显示
        $grid->column('gender')->using(['female' => '女性', 'male' => '男性']);


        // 内容替换
        // 如果需要将这一列的某些值替换为其它的内容来显示：
        $grid->column('cost')->replace(['0.00' => '免费']);

        // 列视图
        // 使用view()方法让当前列渲染一个视图显示输出
        // 默认会传入视图的两个变量，$model为当前行的模型，$value为当前列的值
        // 用这个方法可以很好的渲染出复杂的列内容。
        $grid->column('content')->view('content');


        // Gavatar头像
        // 如果这一列数据是邮箱，你想要显示为Gavatar头像
        $grid->column('email', '头像')->gravatar(45);

        // 文件尺寸
        // 如果这一列的数据是表示文件大小的字节数，可以通过调用filezise方法来显示更有可读性的文字
        $grid->column('file_size')->filesize();

        // 下载链接
        // 如果这一列的数据存储的是上传文件的路径，那么可以通过调用downloadable方法来将这一列设置为可下载链接
        // 没有封装的好 字段路径为 images/v2-3eec3b2149c61e2830420f9cd32087cd_r.jpg
        // 生成的路径却是 http://localhost/public/upload/images/v2-3eec3b2149c61e2830420f9cd32087cd_r.jpg
        $grid->column('file_path')->downloadable();

        // 复制按钮
        // 通过下面的调用，会在这一列的每一行文字前面出现一个复制icon，点击它可以复制它的值
        $grid->column('first_name', '姓甚名谁')->copyable();

        // 二维码
        // 通过下面的调用，会在这一列的每一行文字前面出现一个二维码icon，点击它可以展开一个小弹框，里面会显示这一列值的二维码编码图形
        $grid->column('link', '外链')->qrcode();

        // 显示图片
        // 如果picture字段保存的是图片的完整地址，或者路径，可以通过下面的方式将该列渲染为图片显示
        // 支持多图显示，需要字段输出为数组。
        // $grid->column('picture')->image('', 100, 100);
        // $grid->column('images')->display(function ($images) {
        //     return json_decode($images, true);
        // })->image('', 100, 100);

        // 显示label标签
        // 将字段显示为label标签, 如果字段输出为数组，会显示为多个label标签。
        // $grid->column('status')->label();
        // 设置颜色，默认`success`,可选`danger`、`warning`、`info`、`primary`、`default`、`success`
        // $grid->column('status')->label('danger');
        // 如果需要将status字段的不同的值显示为不同颜色的label
        // $grid->column('status')->label([
        //     1 => 'default',
        //     2 => 'warning',
        //     3 => 'success',
        //     4 => 'info',
        // ]);

        // 显示icon
        // 将字段显示为font-awesome图标, 更多图标参考 http://fontawesome.io/icons/
        // $grid->column('status')->icon([
        //     0 => 'toggle-off',
        //     1 => 'toggle-on',
        // ]);


        // 链接
        // 将字段显示为一个链接。
        // link方法不传参数时，链接的`href`和`text`都是当前列的值
        // $grid->column('link')->link();

        // 表格
        // 将字段显示为一个表格，需要当前列的值为一个二维数组
        // table方法不传参数时，表格的title为二维数组每一列的key
        // $grid->column('no-settings')->display(function () {
        //     return [
        //         [ "language" => "english", "code" => "en", "level" => "easy" ],
        //         [ "language" => "chinese", "code" => "ch", "level" => "difficulty" ],
        //     ];
        // })->table();

        // 进度条
        // 将字段显示为一个进度条，需要当前列的值为一个数值，默认的最大值为100，
        // $style用来设置样式，可选值danger、warning、info、primary、default、success
        // $size用来设置尺寸, 可选值为sm、xs、xxs，$max用来设置最大范围。
        // $grid->column('progress')->progressBar();
        $grid->column('progress')->progressBar($style = 'info', $size = 'sm');

        // loading状态
        // 如果status的值为[1, 2, 3]之一，会显示为一个loading加载icon。
        $grid->column('status')->loading([1, 2, 3], [ 4 => '完成']);

        // 图片轮播
        // 如果字段值为图片数组，可以用下面的调用显示为图片轮播组件
        // $grid->column('images')->display(function ($images) {
        //     return json_decode($images, true);
        // })->carousel($width = 100, $height = 100, '');

        // 日期格式
        // 如果字段值为时间戳，可以用date方法格式化输出
        $grid->column('created_at')->date('Y-m-d');

        // 布尔值显示
        // 将这一列转为bool值之后显示为✓和✗.
        // $grid->column('approved')->display(function () {
        //     return rand(0, 1);
        // })->bool();
        // 也可以按照这一列的值指定显示，比如字段的值为Y和N表示true和false
        // $grid->column('approved')->display(function () {
        //     $values = ['Y', 'N'];
        //     return $values[rand(0, 1)];
        // })->bool(['Y' => true, 'N' => false]);

        // 圆点前缀 - 用起来有问题，dot的映射好像不起作用
        // 在列文字前面加上一个带颜色的圆点，以提供更直观清晰的视觉效果
        $grid->column('another_status')->display(function () {
            $value = rand(1, 4);
            return $value;
        })->using([
            1 => '审核通过',
            2 => '草稿',
            3 => '发布',
            4 => '其它',
        ], '未知')->dot([
            1 => 'danger',
            2 => 'info',
            3 => 'primary',
            4 => 'success',
        ], 'warning');


        $grid->fixColumns(3, -3);
        $grid->paginate(5);
        return $content
            ->header('列的显示')
            ->description('display、collection、以及其他操作')
            ->body($grid); // body方法添加内容
    }
}
