<?php


namespace Riari\Forum\Models\Events;


use Riari\Forum\Contracts\Likes\LikeableContract;

class ModelLiked
{
    public $model;

    public $userId;

    public function __construct(LikeableContract $likeableContract, $userId)
    {
        $this->model = $likeableContract;
        $this->userId = $userId;
    }
}
