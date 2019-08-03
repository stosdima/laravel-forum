<?php


namespace Riari\Forum\Contracts\Likes;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Interface LikableContract
 * @package Riari\Forum\Contracts\Likes
 */
interface LikeableContract
{
    /**
     * Get model primary key
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get morphed class name
     *
     * @return string
     */
    public function getMorphClass();

    /**
     * Collection of likes/dislikes
     *
     * @return MorphMany
     */
    public function marks();

    /**
     * Collection of likes
     *
     * @return MorphMany
     */
    public function likes();

    /**
     * Collection of dislikes
     *
     * @return MorphMany
     */
    public function dislikes();

    /**
     * Likes counter for morphed record
     *
     * @return MorphOne
     */
    public function likesCounter();

    /**
     * Dislikes counter for morphed record
     * @return MorphOne
     */
    public function dislikesCounter();

    /**
     * Model likes_count attribute
     *
     * @return integer
     */
    public function getLikesCountAttribute();

    /**
     * Model dislikes_count attribute
     *
     * @return integer
     */
    public function getDislikesCountAttribute();

    /**
     * Is current logged user liked record
     *
     * @return boolean
     */
    public function getLikedAttribute();

    /**
     * Is current logged user dislike record
     *
     * @return boolean
     */
    public function getDislikedAttribute();

    /**
     * Add a like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     */
    public function like($userId = null);

    /**
     * Remove a like from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     *
     */
    public function unlike($userId = null);

    /**
     * Toggle like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     */
    public function toggleLike($userId = null);

    /**
     * Has the user already liked likeable model.
     *
     * @param int|null $userId
     * @return bool
     */
    public function liked($userId = null);

    /**
     * Add a dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     */
    public function dislike($userId = null);

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     *
     */
    public function undislike($userId = null);

    /**
     * Toggle dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     */
    public function toggleDislike($userId = null);

    /**
     * Has the user already disliked likeable model.
     *
     * @param int|null $userId
     * @return bool
     */
    public function disliked($userId = null);
}
