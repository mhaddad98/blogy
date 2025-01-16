<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\StoreDraftPostRequest;
use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{

    protected $postService;

    public function __construct(
        PostService $postService
    ) {
        $this->postService = $postService;
    }

    public function index(Request $request)
    {
        $filters = [
            'searchPhrase' => $request->input('searchPhrase'),
            'categoryId' => $request->input('categoryId')
        ];

        $posts = $this->postService->activePosts(searchPhrase: $filters['searchPhrase'], categoryId: $filters['categoryId'], paginate: 10);
        $categories = Category::all();

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
        $categories = Category::all();

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
        $post = $this->postService->createPost($user, [...$postAttributes, 'draft' => true]);

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
        //
        // dd($post);
        return Inertia::render('Post/Edit', [
            "post" => $post->only(['id', 'title', 'content', 'categories', 'image', 'draft']),
            "categories" => Category::all(),

        ]);
    }

    public function publish(Request $request, Post $post)
    {

        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'sometimes', function ($attribute, $value, $fail) {
                if (is_string($value) && !is_file($value)) {
                    return;  // Allow string paths for existing images
                }
                // Apply file validation only for new uploads
                if ($value instanceof UploadedFile) {
                    return Validator::make(
                        [$attribute => $value],
                        [$attribute => File::types(['jpg', 'jpeg', 'png', 'svg'])->max(2048)]
                    )->validate();
                }
                $fail("Error On Image...");
            }],
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('images', 'public');
        }

        // Update categories
        if ($request->category) {
            CategoryPost::where('post', $post->id)->delete();
            CategoryPost::create([
                'post' => $post->id,
                'category' => $request->category
            ]);
        }

        // Update post and set draft to false
        $post->update([
            ...$validatedData,
            'draft' => false
        ]);

        return redirect("/post/$post->id");
    }


    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->draft) {
            return $this->publish($request, $post);
        }

        $postAttributes = $request->validated();

        if ($request->hasFile('image')) $postAttributes['image'] = $request->file('image')->store('posts', 'public');
        if ($request->category) {
            CategoryPost::where('post', $post->id)->delete();

            CategoryPost::create([
                'post' => $post->id,
                'category' => $request->category
            ]);
        }

        $post->update($postAttributes);

        return redirect("/post/$post->id");
    }

    public function destroy(Post $post)
    {
        //
        $post->update([
            "deleted" => true
        ]);
        return redirect("/");
    }
}
