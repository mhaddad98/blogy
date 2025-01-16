<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as HttpRequest;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('author', 'categories')
            ->when(Request::input('search'), function ($query, $search) {
                $query->where('content', 'like', "%{$search}%");
            })
            ->when(Request::input('category'), function ($query, $category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            })
            ->whereHas('author', function ($query) {
                $query->where('deleted', false);
            })
            ->where('deleted', false)
            ->where('draft', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => Str::limit($post->content, 50),
                    'authorName' => $post->author->name,
                    'authorId' => $post->author->id,
                    'image' => $post->image,
                    'categories' => $post->categories->map(fn($category) => [
                        'name' => $category->name,
                        'id' => $category->id
                    ]),
                    'date' => $post->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('Home', [
            "posts" => $posts,
            "categories" => Category::all(),
            'filters' => [
                'search' => Request::only('search'),
                'category' => Request::only('category')
            ]
        ]);
    }

    public function userPosts(User $user)
    {
        $authUser = Auth::user();
        $posts = Post::with('author', 'categories')
            ->when(Request::input('search'), function ($query, $search) {
                $query->where('content', 'like', "%{$search}%");
            })
            ->when(Request::input('category'), function ($query, $category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            })
            ->whereHas('author', function ($query) {
                $query->where('deleted', false);
            })
            ->where('user_id', $user->id)
            ->where(function ($query) use ($authUser, $user) {
                if (($authUser && $authUser->id === $user->id) || $authUser->is_admin) {
                    $query->where('deleted', false);
                } else {
                    $query
                        ->where('draft', false)
                        ->where('deleted', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => Str::limit($post->content, 50),
                    'authorName' => $post->author->name,
                    'authorId' => $post->author->id,
                    'image' => $post->image,
                    'categories' => $post->categories->map(fn($category) => [
                        'name' => $category->name,
                        'id' => $category->id
                    ]),
                    'date' => $post->created_at->format('Y-m-d'),
                    'draft' => $post->draft,
                ];
            });

        return Inertia::render('Post/User', [
            'author' => $user->name,
            "posts" => $posts,
            "categories" => Category::all(),
            'filters' => [
                'search' => Request::only('search'),
                'category' => Request::only('category')
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('Post/Create', [
            "categories" => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $postAttributes = $request->validated();

        $user = $request->user();
        $post = $user->posts()->create([
            'title' => $postAttributes['title'],
            'content' => $postAttributes['content'],
            'image' => $postAttributes['image'] ? $postAttributes['image']->store('images', 'public') : null,
        ]);

        CategoryPost::create([
            'post' => $post->id,
            'category' => $postAttributes['category'],
        ]);

        return redirect("/post/{$post->id}");
    }
    public function storeDraft(HttpRequest $request)
    {
        $nonEmptyFields = array_filter($request->only(['title', 'content', 'category', 'image']), function ($value) {
            return !is_null($value) && $value !== '';
        });

        if (empty($nonEmptyFields)) {
            return redirect()->back()->withErrors(['main' => 'At least one field must be provided to store a draft.']);
        }

        $postAttributes = $request->validate([
            'title' => ['sometimes'],
            'content' => ['sometimes',],
            'category' => ['sometimes', 'nullable', 'exists:categories,id'],
            'image' => ['nullable', 'sometimes', function ($attribute, $value, $fail) {
                if (is_string($value) && !is_file($value)) {
                    return;
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

        $user = $request->user();
        $post = $user->posts()->create([
            'title' => $postAttributes['title'],
            'content' => $postAttributes['content'],
            'image' => $postAttributes['image'] ? $postAttributes['image']->store('images', 'public') : null,
            'draft' => true
        ]);

        CategoryPost::create([
            'post' => $post->id,
            'category' => $postAttributes['category'],
        ]);

        return redirect("/post/{$post->id}");
    }

    /**
     * Display the specified resource.
     */
    public function show(HttpRequest $request, Post $post)
    {
        if ($post->deleted) return throw new NotFoundHttpException();

        $authUser = $request->user();
        if (!$authUser || (!$authUser->is_admin && $authUser->id !== $post->author->id)) $post->increment('views');



        $post->load('author', 'categories');
        $formattedPost = [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'authorName' => $post->author->name,
            'authorId' => $post->author->id,
            'image' => $post->image,
            'categories' => $post->categories->map(fn($category) => [
                'name' => $category->name,
                'id' => $category->id
            ]),
            'date' => $post->created_at->format('Y-m-d'),
            'updated' => $post->updated_at->format('Y-m-d'),
            'canEdit' => Auth::user()?->can('update', $post),
            'canDelete' => Auth::user()?->can('delete', $post),
            'draft' => $post->draft,

        ];

        $authorId = $formattedPost['authorId'];
        $categoryIds = collect($formattedPost['categories'])->pluck('id');

        $relatedPosts = Post::query()
            ->with('author', 'categories')
            ->where(function ($query) use ($authorId, $categoryIds) {
                $query->whereHas('author', function ($query) use ($authorId) {
                    $query->where('deleted', false)->where('user_id', $authorId);
                })->orWhereHas('categories', function ($query) use ($categoryIds) {
                    $query->where('deleted', false)->whereIn('categories.id', $categoryIds);
                });
            })
            ->where('deleted', false)
            ->where('draft', false)
            ->where('id', !$post->id)
            ->limit(4)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => Str::limit($post->content, 50),
                    'authorName' => $post->author->name,
                    'authorId' => $post->author->id,
                    'image' => $post->image,
                    'categories' => $post->categories->map(fn($category) => [
                        'name' => $category->name,
                        'id' => $category->id
                    ]),
                    'date' => $post->created_at->format('Y-m-d'),
                ];
            });


        return Inertia::render('Post', [
            "post" => $formattedPost,
            "relatedPosts" => $relatedPosts,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
        // dd($post);
        return Inertia::render('Post/Edit', [
            "post" => $post->only(['id', 'title', 'content', 'categories', 'image', 'draft']),
            "categories" => Category::all(),

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function publish(HttpRequest $request, Post $post)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
        $post->update([
            "deleted" => true
        ]);
        return redirect("/");
    }
}
