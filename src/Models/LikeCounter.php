<?php


namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Riari\Forum\Contracts\Likes\LikeCounterContract;

/**
 * Class Like
 * @package Riari\Forum\Models
 */
class LikeCounter extends BaseModel implements LikeCounterContract
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forum_likes_counter';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'count',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'count' => 'integer',
    ];
    /**
     * Likeable model relation.
     *
     * @return MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
