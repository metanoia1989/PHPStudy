<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 聊天室未读消息
 */
class Count extends Model
{
    public static $ROOMLIST = [1, 2];
    public $timestamps = false;
}
