<?php


namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Riari\Forum\Contracts\Likes\LikeContract;

/**
 * Class Like
 * @package Riari\Forum\Models
 */
class Like extends BaseModel implements LikeContract
{
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'forum_likes';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type_id',
    ];

    /** @return MorphTo */
    public function likeable()
    {
        return $this->morphTo();
    }
}
