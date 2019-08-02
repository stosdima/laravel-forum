<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_posts_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('likeable');
            $table->unsignedInteger('author_id')
                ->comment('Author');
            $table->unsignedTinyInteger('like')
                ->default(0)
                ->comment('Like');
            $table->unsignedTinyInteger('dislike')
                ->default(0)
                ->comment('Dislike');
            $table->unique(['likeable_type', 'likeable_id', 'author_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts_likes');
    }
}
