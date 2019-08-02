<?php


namespace Riari\Forum\Http\Controllers\API;

use Riari\Forum\Models\Like;

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
