<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // laravel 5.4 改变了默认的数据库字符集，现在utf8mb4包括存储emojis支持。
        // MySQL版本小于 v5.7.7
        Schema::defaultStringLength(191);
    }
}
