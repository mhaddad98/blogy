<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\StoreDraftPostRequest;
use App\Http\Requests\UpdateDraftPostRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{

    protected $postService;
    protected $categoryService;

    public function __construct(
        PostService $postService,
        CategoryService $categoryService
    ) {
        $this->postService = $postService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $filters = [
            'searchPhrase' => $request->input('searchPhrase'),
            'categoryId' => $request->input('categoryId')
        ];

        $posts = $this->postService->activePosts(searchPhrase: $filters['searchPhrase'], categoryId: $filters['categoryId'], paginate: 10);
        $categories = $this->categoryService->allCategories();

        return Inertia::render('Home', [
            "posts" => $posts,
            "categories" => $categories,
            "filters" => $filters
        ]);
    }

    public function userPosts(User $user)
    {
        $userId = $user->id;

        $posts = $this->postService->activePosts(null, null, $userId);

        return Inertia::render('Post/User', [
            'author' => $user->name,
            "posts" => $posts,
        ]);
    }

    public function create()
    {
        $categories = $this->categoryService->allCategories();

        return Inertia::render('Post/Create', [
            "categories" => $categories,
        ]);
    }

    public function store(StorePostRequest $request)
    {
        $postAttributes = $request->validated();

        $user = $request->user();

        $post = $this->postService->createPost($user, $postAttributes);

        return redirect("/post/{$post->id}");
    }

    public function storeDraft(StoreDraftPostRequest $request)
    {
        $postAttributes = $request->validated();

        $user = $request->user();
        $post = $this->postService->createPost($user, $postAttributes, draft: true);

        return redirect("/post/{$post->id}");
    }


    public function show(Request $request, Post $post)
    {
        if ($post->deleted) return throw new NotFoundHttpException();

        $this->postService->incrementViews($request, $post);

        $relatedPosts = $this->postService->relatedPosts($post);
        $post = $this->postService->formatPost($post, 'full');

        return Inertia::render('Post', [
            "post" => $post,
            "relatedPosts" => $relatedPosts,
        ]);
    }

    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            "post" => $post->only(['id', 'title', 'content', 'categories', 'image', 'draft']),
            "categories" => $this->categoryService->allCategories(),

        ]);
    }

    public function editDraft(UpdateDraftPostRequest $request, Post $post)
    {
        $postAttributes = $request->validated();

        $request->hasFile('image') ? $image = $request->file('image') : $image = null;
        $request->category ?  $category = $request->category : $category = null;

        $this->postService->updatePost($post, $postAttributes, $category, true, $image);

        return redirect("/post/$post->id");
    }


    public function update(UpdatePostRequest $request, Post $post)
    {
        $postAttributes = $request->validated();

        $request->hasFile('image') ? $image = $request->file('image') : $image = null;
        $request->category ?  $category = $request->category : $category = null;

        $this->postService->updatePost($post, $postAttributes, $category, false, $image);

        return redirect("/post/$post->id");
    }

    public function destroy(Post $post)
    {
        $this->postService->deletePost($post);
        return redirect("/");
    }
}
