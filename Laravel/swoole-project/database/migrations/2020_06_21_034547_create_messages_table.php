<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->smallInteger('room_id');
            $table->string('msg')->comment('文本消息');
            $table->string('img')->comment('图片消息');
            $table->string('type')->comment('消息类型 text 文本 img 图片');
            $table->string('roomType')->comment('来自房间的类型 group single');
            $table->timestamp('created_at'); // 发送消息后不可更改
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
