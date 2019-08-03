<?php


namespace Riari\Forum\Contracts\Likes;


interface LikeableServiceContract
{
    /**
     * Add like to record
     *
     * @param LikeableContract $likeable
     * @param $type
     * @param $user
     */
    public function addLike(LikeableContract $likeable, $type, $user);

    /**
     * Remove like from record
     *
     * @param LikeableContract $likeable
     * @param $type
     * @param $user
     * @return mixed
     */
    public function removeLike(LikeableContract $likeable, $type, $user);

    /**
     * Toggle like of record
     *
     * @param LikeableContract $likeable
     * @param $type
     * @param $user
     * @return mixed
     */
    public function toggleLike(LikeableContract $likeable, $type, $user);
}
