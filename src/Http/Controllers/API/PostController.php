<?php namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Riari\Forum\Contracts\Likes\LikeableServiceContract;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Riari\Forum\Services\LikeableService;

class PostController extends BaseController
{
    /**
     * Return the model to use for this controller.
     *
     * @return Post
     */
    protected function model()
    {
        return new Post;
    }

    /**
     * Return the translation file name to use for this controller.
     *
     * @return string
     */
    protected function translationFile()
    {
        return 'posts';
    }

    /**
     * GET: Return an index of posts by thread ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function index(Request $request)
    {
        $this->validate($request, ['thread_id' => ['required']]);
        $posts = $this->model()
            ->withRequestScopes($request)
            ->where('thread_id', $request->input('thread_id'))
            ->get();
        $thread = Thread::find($request->thread_id);
        if ($thread) {
            $thread->asRead(Auth::id());
        }

        return $this->response($posts);
    }

    /**
     * GET: Return a post.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function fetch($id, Request $request)
    {
        $post = $this->model()->find($id);

        if (is_null($post) || !$post->exists) {
            return $this->notFoundResponse();
        }

        return $this->response($post);
    }

    /**
     * POST: Create a new post.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['thread_id' => ['required'], 'author_id' => ['required'], 'content' => ['required']]);
        $thread = Thread::find($request->input('thread_id'));
        $this->authorize('reply', $thread);
        $data = $request->only(['thread_id', 'post_id', 'author_id', 'content']);
        $data['author_id'] = Auth::id();
        $post = $this->model()->create($data);
        $post->load('thread', 'author');

        return $this->response($post, $this->trans('created'), 201);
    }

    /**
     * @param integer $id
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function like($id)
    {
        $post = $this->model()->findOrFail($id);
        $post->toggleLike(Auth::id());

        return $this->response($post);
    }

    /**
     * @param integer $id
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function dislike($id)
    {
        $post = $this->model()->findOrFail($id);
        $post->toggleDislike(Auth::id());

        return $this->response($post);
    }
}
