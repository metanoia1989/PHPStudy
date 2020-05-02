<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['comment', 'words', 'blog_id'];
    public $timestamps = false;

    public function blog()
    {
        return $this->belongsTo('App\Blog');
    }
}
