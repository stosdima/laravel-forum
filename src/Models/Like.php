<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 *
 * @mixin \Eloquent
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $author_id Author
 * @property int $like Like
 * @property int $dislike Dislike
 * @method static Builder|Like newModelQuery()
 * @method static Builder|Like newQuery()
 * @method static Builder|Like query()
 */
class Like extends BaseModel
{
    protected $table = 'forum_posts_likes';
}
