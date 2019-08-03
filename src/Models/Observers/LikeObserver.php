<?php


namespace Riari\Forum\Models\Observers;

use Riari\Forum\Contracts\Likes\LikeableServiceContract;
use Riari\Forum\Contracts\Likes\LikeContract;
use Riari\Forum\Enums\LikeType;
use Riari\Forum\Models\Events\ModelDisliked;
use Riari\Forum\Models\Events\ModelLiked;
use Riari\Forum\Models\Events\ModelUndisliked;
use Riari\Forum\Models\Events\ModelUnliked;

class LikeObserver
{
    public function created(LikeContract $like)
    {
        $service = app(LikeableServiceContract::class);
        if ($like->type_id === LikeType::LIKE) {
            event(new ModelLiked($like->likeable, $like->user_id));
            $service->incrementLikesCount($like->likeable);
        } else {
            event(new ModelDisliked($like->likeable, $like->user_id));
            $service->incrementDislikesCount($like->likeable);
        }
    }

    public function deleted(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new ModelUnliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementLikesCount($like->likeable);
        } else {
            event(new ModelUndisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementDislikesCount($like->likeable);
        }
    }
}
