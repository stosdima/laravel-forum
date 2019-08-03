<?php

namespace Riari\Forum\Contracts\Likes;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface LikeContract
 *
 * @package Riari\Forum\Contracts\Likes
 */
interface LikeContract
{
    /** @return MorphTo */
    public function likeable();
}
