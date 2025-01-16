<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return Inertia::render('Categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $sort = Request::only('sort')['sort'] ?? 'created_at';

        $posts = Post::with('author', 'categories')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->where('deleted', false)
            ->where('draft', false)
            ->orderBy($sort, 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => Str::limit($post->content, 50),
                    'authorName' => $post->author->name,
                    'authorId' => $post->author->id,
                    'categories' => $post->categories->map(fn($category) => [
                        'name' => $category->name,
                        'id' => $category->id
                    ]),
                    'image' => $post->image,
                    'date' => $post->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('Category', [
            'category' => $category,
            'posts' => $posts,
            'postsCount' => $posts->total()

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
