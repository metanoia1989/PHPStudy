<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['name', 'blog_id'];
    // 新的表名，不使用默认authors表名
    protected $table = 'author';
    public $timestamps = false;

    // 定义关联关系
    public function blog()
    {
        return $this->belongsTo('App\Blog');
    }
}
