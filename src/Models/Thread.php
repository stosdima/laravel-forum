<?php namespace Riari\Forum\Models;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Traits\HasAuthor;
use Riari\Forum\Support\Traits\CachesData;

class Thread extends BaseModel
{
    use SoftDeletes, HasAuthor, CachesData;

    /**
     * Eloquent attributes
     */
    protected $table = 'forum_threads';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category_id', 'author_id', 'title', 'locked', 'pinned', 'reply_count', 'name'];

    /**
     * The attributes that will be appended
     *
     * @var array
     */
    protected $appends = ['last_post', 'subscribed', 'reads_count'];
    /**
     * @var string
     */
    const STATUS_UNREAD = 'unread';

    /**
     * @var string
     */
    const STATUS_UPDATED = 'updated';

    /**
     * Create a new thread model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.preferences.pagination.threads');
    }

    /**
     * Relationship: Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Readers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readers()
    {
        return $this->belongsToMany(
            config('forum.integration.user_model'),
            'forum_threads_read',
            'thread_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Relationship: Subscribers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers()
    {
        return $this->belongsToMany(
            config('forum.integration.user_model'),
            'forum_threads_subscribers',
            'thread_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Relationship: Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        $withTrashed = config('forum.preferences.display_trashed_posts') || Gate::allows('viewTrashedPosts');
        $query = $this->hasMany(Post::class);
        return $withTrashed ? $query->withTrashed() : $query;
    }

    /**
     * Scope: Recent threads.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeRecent($query)
    {
        $time = time();
        $age = strtotime(config('forum.preferences.old_thread_threshold'), 0);
        $cutoff = $time - $age;

        return $query->where('updated_at', '>', date('Y-m-d H:i:s', $cutoff))->orderBy('updated_at', 'desc');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePopular($query)
    {
        $currentMonth = date('m');

        return $query->whereMonth('created_at', $currentMonth)
            ->where('reply_count', '!=', 0)
            ->latest('reply_count');
    }

    /**
     * Attribute: Paginated posts.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPostsPaginatedAttribute()
    {
        return $this->posts()->paginate(config('forum.preferences.pagination.posts'));
    }

    /**
     * Attribute: The last page number of the thread.
     *
     * @return int
     */
    public function getLastPageAttribute()
    {
        return $this->postsPaginated->lastPage();
    }

    /**
     * Attribute: The first post in the thread.
     *
     * @return Post
     */
    public function getFirstPostAttribute()
    {
        return $this->posts()->orderBy('created_at', 'asc')->first();
    }

    /**
     * Attribute: The last post in the thread.
     *
     * @return Post
     */
    public function getLastPostAttribute()
    {
        return $this->posts()
            ->with('author')
            ->orderBy('created_at', 'desc')->first();
    }

    /**
     * Attribute: Creation time of the last post in the thread.
     *
     * @return \Carbon\Carbon
     */
    public function getLastPostTimeAttribute()
    {
        return $this->lastPost->created_at;
    }


    /**
     * Attribute: 'Old' flag.
     *
     * @return boolean
     */
    public function getOldAttribute()
    {
        $age = config('forum.preferences.old_thread_threshold');
        return (!$age || $this->updated_at->timestamp < (time() - strtotime($age, 0)));
    }

    /**
     * Attribute: Currently authenticated reader.
     *
     * @return mixed
     */
    public function getReaderAttribute()
    {
        if (auth()->check()) {
            $reader = $this->readers()->where('forum_threads_read.user_id', auth()->user()->getKey())->first();

            return (!is_null($reader)) ? $reader->pivot : null;
        }

        return null;
    }

    /**
     * Attribute: Get reads count
     *
     * @return int
     */
    public function getReadsCountAttribute()
    {
        return $this->readers()->count();
    }

    public function getSubscribedAttribute()
    {
        if (auth()->check()) {
            $reader = $this->subscribers()
                ->where(
                    'forum_threads_subscribers.user_id',
                    auth()->user()->getKey()
                )->first();

            return (!is_null($reader)) ? true : false;
        }

        return null;
    }


    /**
     * Attribute: Read/unread/updated status for current reader.
     *
     * @return mixed
     */
    public function getUserReadStatusAttribute()
    {
        if (!$this->old && auth()->check()) {
            if (is_null($this->reader)) {
                return self::STATUS_UNREAD;
            }

            return ($this->updatedSince($this->reader)) ? self::STATUS_UPDATED : false;
        }

        return false;
    }

    /**
     * Helper: Mark this thread as read for the given user ID.
     *
     * @param int $userID
     * @return void
     */
    public function markAsRead($userID)
    {
        if (!$this->old) {
            if (is_null($this->reader)) {
                $this->readers()->attach($userID);
            } elseif ($this->updatedSince($this->reader)) {
                $this->reader->touch();
            }
        }

        return $this;
    }

    /**
     * Helper: Mark this thread without old as read for the given user ID.
     *
     * @param int $userID
     * @return void
     */
    public function asRead($userID)
    {
        if (is_null($this->reader)) {
            $this->readers()->attach($userID);
        } elseif ($this->updatedSince($this->reader)) {
            $this->reader->touch();
        }

        return $this;
    }
}
