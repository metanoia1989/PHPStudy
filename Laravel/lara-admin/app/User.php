<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";

    /**
     * 多对多关联
     *
     * @return void
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }

    /**
     * 一对一关联
     *
     * @return void
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
