<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    // 以下字段可以被批量赋值
    protected $fillable = ["title"];

    // 不使用 Laravel 提供的默认时间
    public $timestamps = false;

    // 定义文章模型关系，一篇文章只能有一个作者
    public function author()
    {
        return $this->hasOne('App\Author');
    }

    // 一篇文章有多条评论
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    // 一篇文章可以在多个专题中，一个专题可以包含多篇文章
    public function subjects()
    {
        return $this->belongsToMany('App\Subject', 'blogs_subjects', 'blog_id', 'subject_id');
    }
}
