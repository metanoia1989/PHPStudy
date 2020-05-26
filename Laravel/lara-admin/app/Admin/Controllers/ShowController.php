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

    /**
     * 查询过滤器以及列过滤器
     *
     * @param Content $content
     *
     * @return Content  这个类实现内容区的布局
     */
    public function columnFilter(Content $content)
    {
        $grid = new Grid(new ColumnShow());

        /* 查询过滤器 */
        // 对过滤查询面板的样式做了调整，从原来的弹出modal改为嵌入表格头部，通过点击筛选按钮展开显示，默认是不展开的
        // 在`$grid`实例上操作展开
        // $grid->expandFilter();

        // $grid->filter() 设置查询过滤器
        $grid->filter(function ($filter) {
            // 或者在filter回调里面操作`$filter`实例进行展开
            $filter->expand();

            // 去掉默认的ID过滤器
            $filter->disableIdFilter();

            $filter->column(12, function ($filter) {

                // 添加字段过滤器
                $filter->like('title', '标题字段');

                // 自定义筛选栏下拉菜单
                // scope() 定义为一个查询范围，它将会出现在**筛选按钮的下拉菜单**中
                // scope方法第一个参数为查询的key, 会出现的url参数中，第二个参数是下拉菜单项的label, 如果不填，第一个参数会作为label显示
                // scope方法可以链式调用任何eloquent查询条件
                $filter->scope('male', '男性')->where('gender', 'male');
                // 多条件查询
                $filter->scope('new', '最近修改的')
                    ->whereDate('created_at', '>', date('Y-m-d', strtotime('2020-5-20')))
                    ->orWhere('updated_at', '>', date('Y-m-d', strtotime('2020-5-21')));
                // 关联关系查询
                $filter->scope('address', '地址')->whereHas('profile', function ($query) {
                    $query->whereNotNull('address');
                });

                $filter->scope('trashed', '被软删除的数据')->where('trashed', '1');

                // 过滤器支持的查询类型
                // equal WHERE column= "$input"：
                $filter->equal('title', '标题等于');
                // not equal WHERE column != "$input"：
                $filter->notEqual('title', '标题不等于');
                // ilike WHERE column ILIKE "%$input%"：
                $filter->notEqual('title', '标题不等于');
                // contains 等于like查询
                // $filter->contains('content', '内容');
                // starts with 查询以输入内容开头的title字段数据
                // $filter->startsWith('content', '内容开头');
                // ends with 查询以输入内容结尾的title字段数据
                $filter->endsWith('content', '内容结尾');
                // 大于 WHERE column> "$input"：
                // $filter->gt('cost', "大于多少钱");
                // 小于 WHERE column< "$input"：
                // $filter->lt('cost', "小于多少钱");
                // between WHERE columnBETWEEN "$start" AND "$end"：
                // 范围查询有问题，感觉基本上都需要自定义Between类
                // $filter->between('cost', '价格范围');
                // $filter->between('created_at', '创建的时间日期范围')->datetime();
                // $filter->between('updated_at', '创建的时间范围')->time();
                // in WHERE columnin (...$inputs)
                $filter->in('status', '在指定范围的状态')->multipleSelect([
                    0 => '状态0',
                    1 => '状态1',
                    2 => '状态2',
                    3 => '状态3',
                ]);
                // notIn WHERE columnnot in (...$inputs)
                $filter->notIn('status', '不在指定范围的状态')->multipleSelect(['key' => 'value']);
                // date WHERE DATE(column) = "$input"
                $filter->date('created_time', '查询字段的日期');
                // day WHERE DAY(column) = "$input"
                $filter->day('created_time', '查询字段的天数');
                // month WHERE MONTH(column) = "$input"
                $filter->month('created_time', '查询字段的月份');
                // year WHERE YEAR(column) = "$input"：
                $filter->year('created_time', '查询字段的年份');

                // 用where来构建比较复杂的查询过滤
                // WHERE title LIKE "%$input" OR content LIKE "%$input"
                $filter->where(function ($query) {
                    $query->where('email', 'like', "%{$this->input}%")
                        ->orWhere('cost', 'like', "%{$this->input}%");
                }, '邮箱和价格模糊匹配');
                // WHERE rate>= 6 AND created_at= {$input}
                $filter->where(function ($query) {
                    $query->whereRaw("`cost` > 10 AND `email`= '{$this->input}'");
                }, '价格大于10，然后指定邮箱');
                // 关系查询，查询对应关系profile的字段
                $filter->where(function ($query) {
                    $query->whereHas('profile', function ($query) {
                        $query->where('address', 'like', "%{$this->input}%")->orWhere('email', 'like', "%{$this->input}%");
                    });
                }, '地址或手机号');

                // 表单类型
                // text 表单类型默认是text input，可以设置placeholder：
                $filter->equal('first_name', '名字')->placeholder('请输入名字....');
                $filter->equal('url', 'URL格式')->url();
                $filter->equal('email', 'Email格式')->email();
                $filter->equal('integer', '整数格式')->integer();
                $filter->equal('ip', 'IP格式')->ip();
                $filter->equal('max', 'MAC地址格式')->mac();
                $filter->equal('mobile', '手机格式')->mobile();
                // $options 参考 https://github.com/RobinHerbots/Inputmask
                $filter->equal('decimal', '钱数字格式')->decimal($options = []);
                $filter->equal('currency', '货币格式')->currency($options = []);
                $filter->equal('percentage', '百分比格式')->percentage($options = []);
                $filter->equal('inputmask', '输入掩码格式')->inputmask($options = [], $icon = 'pencil');
                // select 单选下拉菜单
                $filter->equal('select', '单选下拉菜单')->select(["1" => "选项1", "2" => '选项2']);
                // 或者从api获取数据，api的格式参考model-form的select组件
                // $filter->equal('column')->select('api/users');
                // multipleSelect 多选下拉菜单
                $filter->in('multipleSelect', '多选下拉菜单')->multipleSelect(["1" => "选项1", "2" => '选项2']);
                // 或者从api获取数据，api的格式参考model-form的multipleSelect组件
                // $filter->in('column')->multipleSelect('api/users');
                // radio 比较常见的场景是选择分类
                $filter->equal('radio', '单选框')->radio([
                    '' => 'All',
                    0 => '未发布',
                    1 => '已发布',
                ]);
                // checkbox 比较常见的场景是配合whereIn来做范围筛选
                $filter->in('checkbox', '多选框')->checkbox([
                    0 => '未知状态',
                    1 => '状态1',
                    2 => '状态2',
                    3 => '状态3',
                ]);
                // 日期时间周期查询
                // $options的参数和值参考 bootstrap-datetimepicker http://eonasdan.github.io/bootstrap-datetimepicker/Options/
                $filter->equal('datetime', '日期时间查询')->datetime();
                // `date()` 相当于 `datetime(['format' => 'YYYY-MM-DD'])`
                $filter->equal('date', '日期查询')->date();
                // `time()` 相当于 `datetime(['format' => 'HH:mm:ss'])`
                $filter->equal('time', '时间查询')->time();
                // `day()` 相当于 `datetime(['format' => 'DD'])`
                $filter->equal('day', '天数查询')->day();
                // `month()` 相当于 `datetime(['format' => 'MM'])`
                $filter->equal('month', '月份查询')->month();
                // `year()` 相当于 `datetime(['format' => 'YYYY'])`
                $filter->equal('year', '年份查询')->year();

                // 复杂查询过滤器
                // 使用$this->input()来触发复杂的自定义查询
                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'yes':
                            $query->has('relationshipTable');
                            break;
                        case 'no':
                            $query->doesntHave('relationshipTable');
                            break;
                    }
                }, '复杂的查询啊', 'name_for_url_shortcut')->radio([
                    '' => '所有的情况',
                    'yes' => '仅仅有关系的',
                    'no' => '除了有关系之外的'
                ]);

            });

            $filter->column(12, function ($filter) {
                // 多列布局
                // 如果过滤器太多，会把页面拉的很长，将会很影响页面的观感，这个版本将支持过滤器的多列布局, 比如6个过滤器分两列显示
                // 默认会有一个主键字段的过滤器放在第一列，所有左右各三个过滤器一共6个过滤器
                // column方法的第一个参数设置列宽度，可以设置为比例1/2或0.5，或者bootstrap的栅格列宽度比如6，如果三列的话可以设置为1/3或者4
                $filter->column(1/2, function ($filter) {
                    $filter->like('title');
                    $filter->between('rate');
                });

                $filter->column(1/2, function ($filter) {
                    $filter->equal('created_at')->datetime();
                    $filter->between('updated_at')->datetime();
                    $filter->equal('released')->radio([
                        1 => 'YES',
                        0 => 'NO',
                    ]);
                });
            });

            $filter->column(12, function ($filter) {
                // 过滤器组
                // 有时候对同一个字段要设置多中筛选方式， 使用 group() 方法实现
                // 等于 $group->equal();
                // 不等于 $group->notEqual();
                // 大于 $group->gt();
                // 小于 $group->lt();
                // 大于等于 $group->nlt();
                // 小于等于 $group->ngt();
                // 匹配 $group->match();
                // 复杂条件 $group->where();
                // like查询 $group->like();
                // like查询 $group->contains();
                // ilike查询 $group->ilike();
                // 以输入的内容开头 $group->startWith();
                // 以输入的内容结尾 $group->endWith();
                $filter->group('status', '状态', function ($group) {
                    $group->gt('大于');
                    $group->lt('小于');
                    $group->nlt('不小于');
                    $group->ngt('不大于');
                    $group->equal('等于');
                });
            });


        });


        /* 列过滤器 */
        // 这个查询，最终是限制在SQL后面的WHERE条件的，数据表中不存在的字段不能使用这种方式
        // 字符串比较查询
        // 默认对这一列执行等于查询 filter()
        // 执行like查询 filter('like')
        // 用于时间日期的查询 filter('date') filter('time') filter('datetime')
        // 过滤一个或者多个状态的数据，使用多选过滤可以非常方便的实现，只能用于值为数字的字段
        // filter([ 0 => '未知', 1 => '已下单', 2 => '已付款', 3 => '已取消', ]);
        // 过滤一个数字范围 filter('range')
        // 时间日期的范围查询 filter('range', 'date') filter('range', 'time') filter('range', 'datetime')

        // 根据条件显示不同的组件
        $grid->column('title', '标题')->display(function ($title, $column) {
            return $this->status == 1 ? $title : $column->editable();
        })->filter('like');

        $grid->column('full_name', '姓甚名谁')->display(function () {
            return $this->first_name . ' ' . $this->last_name;
        });

        $grid->column('gender', '是男是女')->using([
            'female' => '女性',
            'male' => '男性'
        ])->filter();
        $grid->content('内容');
        $grid->column('email', '邮箱');
        $grid->column('cost')->replace(['0.00' => '免费']);
        // $grid->column('email', '头像')->gravatar(45);
        $grid->column('file_size')->filesize();
        $grid->column('file_path')->downloadable();
        $grid->column('first_name', '姓甚名谁')->copyable();
        $grid->column('link', '外链')->qrcode();
        $grid->column('status')->loading([1, 2, 3], [ 4 => '完成'])->filter([ 0 => '未知', 1 => '已下单', 2 => '已付款', 3 => '已取消', ]);;
        $grid->column('price', '价格')->display(function() {
            return rand(100, 105);
        })->filter('range');
        $grid->column('created_at')->date('Y-m-d')->filter('date');
        $grid->column('updated_at')->date('Y-m-d')->filter('range', 'date');

        $grid->fixColumns(3, -3);
        $grid->paginate(5);
        return $content
            ->header('列的显示')
            ->description('display、collection、以及其他操作')
            ->body($grid); // body方法添加内容
    }

    /**
     * laravel-admin简单模型表格功能演示
     *
     * @return void
     */
    public function simple(Content $content)
    {
        $grid = new Grid(new ColumnShow());

        // 启用 Laravel-admin 快捷键
        // 从v1.7.2版本开始，在grid页面加入了几个操作快捷键，
        // s	快捷搜索聚焦
        // f	展开或者隐藏过滤器
        // r	刷新页面
        // c	进入创建页面
        // left	跳转上一页
        // right	跳转下一页
        $grid->enableHotKeys();

        // 快捷创建表单
        // 在表格中开启这个功能之后，会在表格头部增加一个form表单来创建数据，
        // 对于一些简单的表格页面，可以方便快速创建数据，不用跳转到创建页面操作
        // 需要注意的是，快捷创建表单中的每一项，在form表单页面要设置相同类型的表单项。
        // quickCreate 方法跟 fixColumns 冲突，设置后者，快捷创建表单将不会显示
        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('title', '标题');
            $create->email('email', '邮箱');
            $create->password('password', '密码输入框');
            $create->mobile('mobile', '手机号输入框');
            $create->integer('integer', '整数输入框');
            $create->select('select', '单选框')->options([
                1 => 'foo',
                2 => 'bar',
            ]);
            $create->multipleSelect('multipleSelect', '多选框')->options([
                1 => 'foo',
                2 => 'bar',
            ]);
            $create->datetime('datetime', '日期时间选择');
            $create->time('time', '时间选择');
            $create->date('date', '日期选择');
        });

        // 规格选择器
        // 用来构建类似淘宝或京东商品的规格选择

        // 快捷搜索
        // 快捷搜索是除了filter之外的另一个表格数据搜索方式，用来快速过滤你想要的数据
        // 通过给quickSearch方法传入不同的参数，来设置不同的搜索方式
        // $grid->quickSearch();
        // Like搜索 通过设置字段名称来进行简单的like查询
        // 提交后模型会执行下面的查询 $model->where('title', 'like', "%{$input}%");
        // $grid->quickSearch('title');
        // 对多个字段做like查询 where ... or ... like....
        // $grid->quickSearch('title', 'email', 'content');
        // 自定义搜索 更灵活的控制搜索条件
        // 闭包的参数$query为你填入搜索框中的内容
        // $grid->quickSearch(function ($model, $query) {
        //     $model->where('title', $query)->orWhere('content', 'like', "%{$query}%");
        // });
        // 快捷语法搜索
        // 参考了Github的搜索语法，来进行快捷搜索，无需传递任何参数
        // 比较查询
        // title:foo 、title:!foo
        // $model->where('title', 'foo');
        // $model->where('title', '!=', 'foo');
        // rate:>10、rate:<10、rate:>=10、rate:<=10
        // In、NotIn查询
        // status:(1,2,3,4)、status:!(1,2,3,4)
        // Between查询
        // score:[1,10]
        // 时间日期函数查询
        // created_at:date,2019-06-08
        // created_at:time,09:57:45
        // created_at:day,08
        // Like查询
        // content:%Laudantium%
        // 正则查询
        // username:/song/
        // $model->where('username', 'REGEXP', 'song');
        // Label作为查询字段名称
        // 比如设置了`user_status`的表头列名为`用户状态`
        // $grid->column('user_status', '用户状态');
        // 可以填入用户状态:(1,2,3)来执行下面的查询
        // $model->whereIn('user_status', [1, 2, 3]);

        $grid->quickSearch();


        // 模型表格字段列表
        $grid->column('title', '标题')->display(function ($title, $column) {
            return $this->status == 1 ? $title : $column->editable();
        })->filter('like');
        $grid->column('email', '邮箱');
        $grid->column('full_name', '姓甚名谁')->display(function () {
            return $this->first_name . ' ' . $this->last_name;
        });
        $grid->column('gender', '是男是女')->using([
            'female' => '女性',
            'male' => '男性'
        ])->filter();
        $grid->content('内容');
        $grid->column('cost')->replace(['0.00' => '免费']);
        $grid->column('file_size')->filesize();
        $grid->column('file_path')->downloadable();
        $grid->column('first_name', '姓甚名谁')->copyable();
        $grid->column('link', '外链')->qrcode();
        $grid->column('status')->loading([1, 2, 3], [ 4 => '完成'])->filter([ 0 => '未知', 1 => '已下单', 2 => '已付款', 3 => '已取消', ]);;
        $grid->column('price', '价格')->display(function() {
            return rand(100, 105);
        })->filter('range');
        $grid->column('created_at')->date('Y-m-d')->filter('date');
        $grid->column('updated_at')->date('Y-m-d')->filter('range', 'date');

        // $grid->fixColumns(3, -3);
        $grid->paginate(5);
        return $content
            ->header('模型表格简单功能')
            ->description('快捷键等功能')
            ->body($grid); // body方法添加内容

    }
}
