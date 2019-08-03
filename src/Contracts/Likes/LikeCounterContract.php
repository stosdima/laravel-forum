<?php

namespace Riari\Forum\Contracts\Likes;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface LikeCounterContract
 *
 * @package Riari\Forum\Contracts\Likes
 */
interface LikeCounterContract
{
    /** @return MorphTo */
    public function likeable();
}
