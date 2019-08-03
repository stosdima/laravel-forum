<?php


namespace Riari\Forum\Models\Events;


use Riari\Forum\Contracts\Likes\LikeableContract;

class ModelUnliked
{
    public $model;

    public $userId;

    public function __construct(LikeableContract $likeableContract, $userId)
    {
        $this->model = $likeableContract;
        $this->userId = $userId;
    }
}
