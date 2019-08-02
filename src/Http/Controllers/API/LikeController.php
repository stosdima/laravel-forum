<?php namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Like;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class LikeController extends BaseController
{
    /**
     * Return the model to use for this controller.
     *
     * @return Like
     */
    protected function model()
    {
        return new Like;
    }

    /**
     * Return the translation file name to use for this controller.
     *
     * @return string
     */
    protected function translationFile()
    {
        return 'likes';
    }

}
