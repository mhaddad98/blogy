<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Inertia\Inertia;


class CategoryController extends Controller
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


    public function index()
    {
        return Inertia::render('Categories', [
            "categories" => $this->categoryService->allCategories(),
        ]);
    }

    public function show(Request $request, Category $category)
    {
        $orderBy = $request->input('orderBy') ?? 'created_at';

        $posts = $this->postService->activePosts(paginate: 10, categoryId: $category->id, orderBy: $orderBy);

        return Inertia::render('Category', [
            'category' => $category,
            'posts' => $posts,
            'postsCount' => $posts->total()

        ]);
    }
}
