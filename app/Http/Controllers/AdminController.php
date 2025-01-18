<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\PostService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;


class AdminController extends Controller
{

    protected $postService;
    protected $categoryService;
    protected $userService;

    public function __construct(
        PostService $postService,
        CategoryService $categoryService,
        UserService $userService
    ) {
        $this->postService = $postService;
        $this->categoryService = $categoryService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        return Inertia::render('Admin/Index');
    }

    public function usersIndex(Request $request)
    {
        $deleted = filter_var($request->input('deleted') || false, FILTER_VALIDATE_BOOLEAN);

        $users = $this->userService->getUsers($deleted);

        return Inertia::render('Admin/UsersDashboard', ['users' => $users]);
    }


    public function editUser(UpdateUserRequest $request, User $user)
    {
        $userAttributes = $request->validated();

        $this->userService->updateUser($user, $userAttributes);

        return redirect()->back()->with('message', 'User updated successfully!');
    }

    public function deleteUser(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) return redirect()->back()->withErrors(["main" => "You Cannot Delete Your own admin account"]);
        $this->userService->deleteUser($user);

        return redirect()->back()->with('message', 'User updated successfully!');;
    }

    public function restoreUser(User $user)
    {
        $this->userService->restoreUser($user);

        return redirect()->back()->with('message', 'User updated successfully!');
    }

    public function postsIndex(Request $request)
    {
        $deleted = filter_var($request->input('deleted') || false, FILTER_VALIDATE_BOOLEAN);
        $posts = $this->postService->activePosts(active: !$deleted, formate: 'admin');

        return Inertia::render('Admin/PostsDashboard', ['posts' => $posts]);
    }


    public function deletePost(Post $post)
    {
        $this->postService->deletePost($post);

        return redirect()->back()->with('message', 'Post updated successfully!');;
    }

    public function restorePost(Post $post)
    {
        $this->postService->restorePost($post);

        return redirect()->back()->with('message', 'Post updated successfully!');
    }

    public function categoriesCreate(Request $request)
    {
        return Inertia::render('Admin/CreateCategories');
    }

    public function categoriesIndex(Request $request)
    {
        $deleted = filter_var($request->input('deleted') || false, FILTER_VALIDATE_BOOLEAN);
        $categories = $this->categoryService->getCategories($deleted);

        return Inertia::render('Admin/CategoriesDashboard', ['categories' => $categories]);
    }


    public function categoriesStore(StoreCategoryRequest $request)
    {
        $categoryAttributes = $request->validated();

        $this->categoryService->createCategory($categoryAttributes);

        return redirect('/admin/categories')->with('message', 'Category Added successfully!');
    }

    public function editCategory(UpdateCategoryRequest $request, Category $category)
    {
        $categoryAttributes = $request->validated();

        $this->categoryService->updateCategory($category, $categoryAttributes);

        return redirect()->back()->with('message', 'Category updated successfully!');
    }

    public function deleteCategory(Category $category)
    {
        $this->categoryService->deleteCategory($category);

        return redirect()->back()->with('message', 'Category deleted successfully!');;
    }

    public function restoreCategory(Category $category)
    {
        $this->categoryService->restoreCategory($category);

        return redirect()->back()->with('message', 'Category restored successfully!');
    }
}
