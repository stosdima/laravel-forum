<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('likeable');
            $table->unsignedInteger('user_id');
            $table->enum('type_id', [
                'like',
                'dislike'
            ])->default('like');
            $table->unsignedInteger('count')
                ->default(0);
            $table->unique([
                'likeable_id',
                'likeable_type',
                'type_id'
            ], 'like_counter_unique');
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
        Schema::dropIfExists('forum_likes');
    }
}
