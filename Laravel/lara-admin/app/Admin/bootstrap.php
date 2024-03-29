<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use App\Admin\Extensions\Popover;
use Encore\Admin\Grid\Column;

Encore\Admin\Form::forget(['map', 'editor']);

// 扩展列功能
Column::extend('customColor', function ($value, $color) {
    return "<span style='color: $color'>$value</span>";
});

// 注册扩展类
Column::extend('customPopover', Popover::class);
