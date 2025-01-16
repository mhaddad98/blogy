<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;


class AdminController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render('Admin/Index');
    }

    public function usersIndex(Request $request)
    {
        $deleted = filter_var($request->input('deleted') || false, FILTER_VALIDATE_BOOLEAN);

        $users = User::where('deleted', $deleted)->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'verified' => $user->hasVerifiedEmail(),
                'admin' => $user->is_admin,
                'deleted' => $user->deleted,
            ];
        });

        return Inertia::render('Admin/UsersDashboard', ['users' => $users]);
    }

    public function categoriesIndex(Request $request)
    {
        $deleted = filter_var($request->input('deleted') || false, FILTER_VALIDATE_BOOLEAN);
        $categories = Category::where('deleted', $deleted)->orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/CategoriesDashboard', ['categories' => $categories]);
    }

    public function postsIndex(Request $request)
    {
        $deleted = filter_var($request->input('deleted') || false, FILTER_VALIDATE_BOOLEAN);
        $posts = Post::query()
            ->with('author')
            ->where('deleted', $deleted)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => Str::limit($post->content, 50),
                    'authorName' => $post->author->name,
                    'draft' => $post->draft,
                    'views' => $post->views,
                    'deleted' => $post->deleted,
                ];
            });

        return Inertia::render('Admin/PostsDashboard', ['posts' => $posts]);
    }

    public function categoriesCreate(Request $request)
    {
        return Inertia::render('Admin/CreateCategories');
    }

    public function categoriesStore(Request $request)
    {
        $userAttributes = $request->validate([
            'name' => ['required', 'min:3'],
            'description' => ['required', 'min:10'],
        ]);

        Category::create($userAttributes);

        return redirect('/admin/categories')->with('message', 'Category Added successfully!');
    }

    public function editUser(Request $request, User $user)
    {

        $userAttributes = $request->all();

        if (empty($userAttributes) || count(array_filter($userAttributes, fn($value) => !is_null($value))) === 0) {
            return redirect()->back()->withErrors(['userUpdate' => "Changes Must be Valid"]);
        }


        $user->update([
            'name' => $userAttributes['name'] ?? $user->name,
            'email' => $userAttributes['email'] ?? $user->email,
            'is_admin' => $userAttributes['is_admin'] ?? $user->is_admin,
        ]);


        return redirect()->back()->with('message', 'User updated successfully!');
    }

    public function deleteUser(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) return redirect()->back()->withErrors(["main" => "You Cannot Delete Your own admin account"]);
        $user->update(['deleted' => true]);

        return redirect()->back()->with('message', 'User updated successfully!');;
    }

    public function restoreUser(User $user)
    {
        $user->update(['deleted' => false]);

        return redirect()->back()->with('message', 'User updated successfully!');
    }

    public function editCategory(Request $request, Category $category)
    {
        $userAttributes = $request->all();

        if (empty($userAttributes) || count(array_filter($userAttributes, fn($value) => !is_null($value))) === 0) {
            return redirect()->back()->withErrors(['categoryUpdate' => "Changes Must be Valid"]);
        }


        $category->update([
            'name' => $userAttributes['name'] ?? $category->name,
            'description' => $userAttributes['description'] ?? $category->description,
        ]);


        return redirect()->back()->with('message', 'Category updated successfully!');
    }

    public function deleteCategory(Category $category)
    {
        $category->update(['deleted' => true]);

        return redirect()->back()->with('message', 'Category updated successfully!');;
    }

    public function restoreCategory(Category $category)
    {
        $category->update(['deleted' => false]);

        return redirect()->back()->with('message', 'Category updated successfully!');
    }

    public function deletePost(Post $post)
    {
        $post->update(['deleted' => true]);

        return redirect()->back()->with('message', 'Post updated successfully!');;
    }

    public function restorePost(Post $post)
    {
        $post->update(['deleted' => false]);

        return redirect()->back()->with('message', 'Post updated successfully!');
    }
}
