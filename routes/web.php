<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\UnVerified;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PostController::class, 'index']);

Route::get('/about', function () {
    return Inertia::render("About");
});

Route::get('/post/create', [PostController::class, 'create'])->middleware('auth');
Route::post('/post', [PostController::class, 'store'])->middleware('auth');

Route::post('/post/draft', [PostController::class, 'storeDraft'])->middleware('auth');

Route::get('/post/user/{user}', [PostController::class, 'userPosts'])->middleware('auth');
Route::delete('/post/{post}', [PostController::class, 'destroy'])->middleware('auth');

Route::get('/post/{post}', [PostController::class, 'show']);

Route::get('/post/{post}/edit', [PostController::class, 'edit'])->can('update', 'post');
Route::post('/post/{post}/edit', [PostController::class, 'update'])->can('update', 'post');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);

    Route::get('/reset', [ProfileController::class, 'resetShow']);
    Route::post('/reset/send/{email}', [ProfileController::class, 'resetSend']);
    Route::post('/reset', [ProfileController::class, 'resetStore']);
    Route::post('/reset/new', [ProfileController::class, 'newStore']);
    
});

Route::delete('/logout', [SessionController::class, 'destroy'])->middleware('auth');

Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth');
Route::get('/changepassword', [ProfileController::class, 'show'])->middleware('auth');
Route::post('/changepassword', [ProfileController::class, 'store'])->middleware('auth');

Route::get('/profile/verify', [ProfileController::class, 'verifyShow'])->middleware('auth', UnVerified::class);
Route::post('/profile/send', [ProfileController::class, 'sendCode'])->middleware('auth', UnVerified::class);
Route::post('/profile/verify', [ProfileController::class, 'verifyStore'])->middleware('auth', UnVerified::class);

Route::get('/profile/prev', function () {
    return view('mail.otp');
});

Route::get('/admin', [AdminController::class, 'index'])->middleware('auth', Admin::class);

Route::get('/admin/categories/add', [AdminController::class, 'categoriesCreate'])->middleware('auth', Admin::class);
Route::get('/admin/categories', [AdminController::class, 'categoriesIndex'])->middleware('auth', Admin::class);

Route::post('/admin/categories', [AdminController::class, 'categoriesStore'])->middleware('auth', Admin::class);
Route::patch('/admin/categories/{category}/edit', [AdminController::class, 'editCategory'])->middleware('auth', Admin::class);
Route::delete('/admin/categories/{category}', [AdminController::class, 'deleteCategory'])->middleware('auth', Admin::class);
Route::patch('/admin/categories/{category}', [AdminController::class, 'restoreCategory'])->middleware('auth', Admin::class);

Route::get('/admin/users', [AdminController::class, 'usersIndex'])->middleware('auth', Admin::class);

Route::patch('/admin/user/{user}/edit', [AdminController::class, 'editUser'])->middleware('auth', Admin::class);
Route::delete('/admin/user/{user}', [AdminController::class, 'deleteUser'])->middleware('auth', Admin::class);
Route::patch('/admin/user/{user}', [AdminController::class, 'restoreUser'])->middleware('auth', Admin::class);

Route::get('/admin/posts', [AdminController::class, 'postsIndex'])->middleware('auth', Admin::class);
Route::delete('/admin/posts/{post}', [AdminController::class, 'deletePost'])->middleware('auth', Admin::class);
Route::patch('/admin/posts/{post}', [AdminController::class, 'restorePost'])->middleware('auth', Admin::class);
