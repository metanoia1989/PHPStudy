<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        foreach(range(0, 10) as $i) {
            User::create([
                'name' => 'username'.$i,
                'password' => 'password'.$i,
                'email' => "email$i@job.com",
            ]);
        }
    }
}
