<?php


namespace Riari\Forum\Services;

use Illuminate\Support\Facades\Auth;
use Riari\Forum\Contracts\Likes\LikeableContract;
use Riari\Forum\Contracts\Likes\LikeableServiceContract;
use Riari\Forum\Enums\LikeType;
use Riari\Forum\Enums\LikeTypeNotDefined;
use Riari\Forum\Enums\UserNotDefined;

class LikeableService implements LikeableServiceContract
{
    /**
     * @param LikeableContract $likeable
     * @param $type
     * @param $user
     * @throws LikeTypeNotDefined
     * @throws UserNotDefined
     */
    public function addLike(LikeableContract $likeable, $type, $user)
    {
        $type = $this->getMarkType($type);
        $user = $this->getUser($user);
        $like = $likeable->marks()
            ->where('user_id', $user)
            ->first();
        if (!$like) {
            $likeable->likes()->create([
                'user_id' => $user,
                'type_id' => $type
            ]);

            return;
        }

        if ($like->type_id === $type) {
            return;
        }

        $like->delete();
        $likeable->likes()->create([
            'user_id' => $user,
            'type_id' => $type
        ]);
    }

    /**
     * @param LikeableContract $likeable
     * @param $type
     * @param $user
     * @return mixed|void
     * @throws LikeTypeNotDefined
     * @throws UserNotDefined
     */
    public function removeLike(LikeableContract $likeable, $type, $user)
    {
        $user = $this->getUser($user);
        $like = $likeable->marks()->where([
            'user_id' => $user,
            'type_id' => $this->getMarkType($type),
        ])->first();
        if (!$like) {
            return;
        }

        $like->delete();
    }

    /**
     * @param LikeableContract $likeable
     * @param $type
     * @param $user
     * @return mixed|void
     * @throws LikeTypeNotDefined
     * @throws UserNotDefined
     */
    public function toggleLike(LikeableContract $likeable, $type, $user)
    {
        $user = $this->getUser($user);
        $likeExists = $likeable->marks()->where([
            'user_id' => $user,
            'type_id' => $this->getMarkType($type),
        ])->exists();
        if ($likeExists) {
            $this->removeLike($likeable, $type, $user);
        } else {
            $this->addLike($likeable, $type, $user);
        }
    }

    /**
     * @param $type
     * @return mixed
     * @throws LikeTypeNotDefined
     */
    private function getMarkType($type)
    {
        $type = strtoupper($type);
        if (!defined("\\Riari\\Forum\\Enums\\LikeType::{$type}")) {
            throw new LikeTypeNotDefined('Current type didn`t exists');
        }

        return constant("\\Riari\\Forum\\Enums\\LikeType::{$type}");
    }

    /**
     * @param $user
     * @return int|null
     * @throws UserNotDefined
     */
    private function getUser($user)
    {
        if (is_null($user)) {
            $user = Auth::id();
        }

        if (!$user) {
            throw new UserNotDefined();
        }

        return $user;
    }

    /**
     *
     * Increment the total like count stored in the counter.
     *
     * @param LikeableContract $likeable
     * @return void
     */
    public function incrementLikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->likesCounter()->first();
        if (!$counter) {
            $counter = $likeable->likesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::LIKE,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Decrement the total like count stored in the counter.
     *
     * @param LikeableContract $likeable
     * @return void
     */
    public function decrementLikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->likesCounter()->first();
        if (!$counter) {
            return;
        }
        $counter->decrement('count');
    }

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param LikeableContract $likeable
     * @return void
     */
    public function incrementDislikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->dislikesCounter()->first();
        if (!$counter) {
            $counter = $likeable->dislikesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::DISLIKE,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param LikeableContract $likeable
     * @return void
     */
    public function decrementDislikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->dislikesCounter()->first();
        if (!$counter) {
            return;
        }
        $counter->decrement('count');
    }

    /**
     * @param LikeableContract $likeable
     * @param $type
     * @param $user
     * @return mixed
     * @throws LikeTypeNotDefined
     * @throws UserNotDefined
     */
    public function isMarked(LikeableContract $likeable, $type, $user)
    {
        $user = $this->getUser($user);
        $type = $this->getMarkType($type);

        return $likeable->marks()->where([
            'user_id' => $user,
            'type_id' => $type,
        ])->exists();
    }
}
