<?php


namespace Riari\Forum\Models\Traits;


use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Riari\Forum\Contracts\Likes\LikeableServiceContract;
use Riari\Forum\Contracts\Likes\LikeCounterContract;
use Riari\Forum\Enums\LikeType;
use Riari\Forum\Models\Like;

trait Likeable
{
    /** @return MorphMany */
    public function marks()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Collection of likes
     *
     * @return MorphMany
     */
    public function likes()
    {
        return $this->marks()->where('type_id', LikeType::LIKE);
    }

    /**
     * Collection of dislikes
     *
     * @return MorphMany
     */
    public function dislikes()
    {
        return $this->marks()->where('type_id', LikeType::DISLIKE);
    }

    /**
     * Likes counter
     *
     * @return MorphOne
     */
    public function likesCounter()
    {
        return $this->morphOne(app(LikeCounterContract::class), 'likeable')->where('type_id', LikeType::LIKE);
    }

    /**
     * Dislikes counter
     *
     * @return MorphOne
     */
    public function dislikesCounter()
    {
        return $this->morphOne(app(LikeCounterContract::class), 'likeable')->where('type_id', LikeType::DISLIKE);
    }

    /**
     * Add like
     *
     * @param null $user
     * @return mixed
     */
    public function like($user = null)
    {
        return app(LikeableServiceContract::class)->addLike($this, LikeType::LIKE, $user);
    }

    /**
     * Remove like
     *
     * @param null $user
     * @return mixed
     */
    public function unlike($user = null)
    {
        return app(LikeableServiceContract::class)->removeLike($this, LikeType::LIKE, $user);
    }

    /**
     * Toggle like
     *
     * @param null $user
     * @return mixed
     */
    public function toggleLike($user = null)
    {
        return app(LikeableServiceContract::class)->toggleLike($this, LikeType::LIKE, $user);
    }

    /**
     * Is record already liked
     *
     * @param null $user
     * @return mixed
     */
    public function liked($user = null)
    {
        return app(LikeableServiceContract::class)->isMarked($this, LikeType::LIKE, $user);
    }

    /**
     * Add dislike
     *
     * @param null $user
     * @return mixed
     */
    public function dislike($user = null)
    {
        return app(LikeableServiceContract::class)->isMarked($this, LikeType::DISLIKE, $user);
    }

    /**
     * Remove dislike
     *
     * @param null $user
     * @return mixed
     */
    public function undislike($user = null)
    {
        return app(LikeableServiceContract::class)->removeLike($this, LikeType::DISLIKE, $user);
    }

    /**
     * Toggle dislike
     *
     * @param null $user
     * @return mixed
     */
    public function toggleDislike($user = null)
    {
        return app(LikeableServiceContract::class)->toggleLike($this, LikeType::DISLIKE, $user);
    }

    /**
     * Check is record disliked
     *
     * @param null $user
     * @return mixed
     */
    public function disliked($user = null)
    {
        return app(LikeableServiceContract::class)->isMarked($this, LikeType::DISLIKE, $user);
    }

    /**
     * Model likes_counter attribute
     *
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likesCounter ? $this->likesCounter->count : 0;
    }

    /**
     * Model dislikes_counter attribute
     *
     * @return int
     */
    public function getDislikesCountAttribute()
    {
        return $this->disLikesCounter ? $this->disLikesCounter->count : 0;
    }

    public function getLikedAttribute()
    {
        return $this->liked();
    }

    public function getDislikedAttribute()
    {
        return $this->disliked();
    }
}
