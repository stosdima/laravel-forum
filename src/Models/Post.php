<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Contracts\Likes\LikeableContract;
use Riari\Forum\Models\Traits\HasAuthor;
use Riari\Forum\Models\Traits\Likeable;
use Riari\Forum\Support\Traits\CachesData;

class Post extends BaseModel implements LikeableContract
{
    use SoftDeletes, HasAuthor, CachesData, Likeable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forum_posts';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    protected $fillable = ['thread_id', 'author_id', 'post_id', 'content'];

    protected $appends = ['liked', 'disliked', 'likes_count', 'dislikes_count'];

    /**
     * Create a new post model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setPerPage(config('forum.preferences.pagination.posts'));
    }

    /**
     * Relationship: Thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class)->withTrashed();
    }

    /**
     * Relationship: Parent post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Relationship: Child posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Post::class, 'post_id')->withTrashed();
    }

    /**
     * Attribute: First post flag.
     *
     * @return boolean
     */
    public function getIsFirstAttribute()
    {
        return $this->id == $this->thread->firstPost->id;
    }

    /**
     * Helper: Sequence number in thread.
     *
     * @return int
     */
    public function getSequenceNumber()
    {
        foreach ($this->thread->posts as $index => $post) {
            if ($post->id == $this->id) {
                return $index + 1;
            }
        }
    }
}
