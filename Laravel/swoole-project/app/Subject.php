<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];
    public $timestamps = false;

    public function blogs()
    {
        return $this->belongsToMany('App\Blog', 'blogs_subjects', 'subject_id', 'blog_id');
    }
}
