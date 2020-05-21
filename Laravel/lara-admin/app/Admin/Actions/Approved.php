<?php

namespace App\Admin\Actions;

use App\ColumnShow;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class Approved extends Action
{
    protected $selector = '.approved';

    // 在页面点击这一列的图表之后，发送请求到后端的handle方法执行
    public function handle(ColumnShow $columnShow)
    {
        // 切换`approved`字段的值并保存
        $columnShow->approved = (int) !$columnShow->approved;
        $columnShow->save();

        // 保存之后返回新的html到前端显示
        $html = $columnShow->approved ? "<i class='fa fa-star-o'></i>" : "<i class='fa fa-star'></i>";
        return $this->response()->html($html);
    }

    // 根据 `approved` 字段的值来显示不同的图标
    public function display($approved)
    {
        return $approved ? "<i class='fa fa-star-o'></i>" : "<i class='fa fa-star'></i>";
    }
}
