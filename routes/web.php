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


Route::get('/post/{post}', [PostController::class, 'show']);

Route::middleware('auth')->group(function () {
    Route::get('/post/create', [PostController::class, 'create']);
    Route::post('/post', [PostController::class, 'store']);

    Route::post('/post/draft', [PostController::class, 'storeDraft']);

    Route::get('/post/user/{user}', [PostController::class, 'userPosts']);
    Route::delete('/post/{post}', [PostController::class, 'destroy']);


    Route::get('/post/{post}/edit', [PostController::class, 'edit'])->can('update', 'post');
    Route::post('/post/{post}/edit', [PostController::class, 'update'])->can('update', 'post');
    Route::post('/post/{post}/edit/draft', [PostController::class, 'editDraft'])->can('update', 'post');

    Route::get('/profile', [ProfileController::class, 'index']);
    Route::get('/changepassword', [ProfileController::class, 'show']);
    Route::post('/changepassword', [ProfileController::class, 'store']);

    Route::middleware(UnVerified::class)->group(function () {
        Route::get('/profile/verify', [ProfileController::class, 'verifyShow']);
        Route::post('/profile/send', [ProfileController::class, 'sendCode']);
        Route::post('/profile/verify', [ProfileController::class, 'verifyStore']);
    });
});


Route::middleware(['auth', Admin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);

    Route::get('/admin/categories/add', [AdminController::class, 'categoriesCreate']);
    Route::get('/admin/categories', [AdminController::class, 'categoriesIndex']);

    Route::post('/admin/categories', [AdminController::class, 'categoriesStore']);
    Route::patch('/admin/categories/{category}/edit', [AdminController::class, 'editCategory']);
    Route::delete('/admin/categories/{category}', [AdminController::class, 'deleteCategory']);
    Route::patch('/admin/categories/{category}', [AdminController::class, 'restoreCategory']);

    Route::get('/admin/users', [AdminController::class, 'usersIndex']);

    Route::patch('/admin/user/{user}/edit', [AdminController::class, 'editUser']);
    Route::delete('/admin/user/{user}', [AdminController::class, 'deleteUser']);
    Route::patch('/admin/user/{user}', [AdminController::class, 'restoreUser']);

    Route::get('/admin/posts', [AdminController::class, 'postsIndex']);
    Route::delete('/admin/posts/{post}', [AdminController::class, 'deletePost']);
    Route::patch('/admin/posts/{post}', [AdminController::class, 'restorePost']);
});
