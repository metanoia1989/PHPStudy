<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 文章表
        Schema::create('blogs', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('title');
        });
        // 作者表
        Schema::create('author', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->integer('blog_id');
        });
        // 评论表
        Schema::create('comments', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('content');
            $table->integer('words');
            $table->integer('blog_id');
        });
        // 专题表
        Schema::create('subjects', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
        });
        // 文章与专题关系表
        Schema::create('blogs_subjects', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('blog_id');
            $table->integer('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('author');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('blog_subjects');
    }
}
