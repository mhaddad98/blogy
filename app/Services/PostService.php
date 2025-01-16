<?php

namespace App\Services;

use App\Models\CategoryPost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Str;
use InvalidArgumentException;

use function Pest\Laravel\get;

class PostService
{

    public function formatPost($post, $formate)
    {
        if (!$formate) $formate = 'short';

        $formateOptions = [
            'short' => [
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
            ],
            'full' => [
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
            ]
        ];

        return $formateOptions[$formate];
    }

    public function allPosts(
        $optionWhere = null,
        $formate = null,
        $paginate = null,
        $limit = null,
        $orderBy = 'created_at',
        $sort = 'desc'
    ) {

        $query = Post::query()
            ->with('author', 'categories')
            ->when($optionWhere, function ($query, $optionWhere) {
                foreach ($optionWhere as $condition) {
                    $condition($query);
                }
            })
            ->whereHas('author', function ($query) {
                $query->where('deleted', false);
            })
            ->where('deleted', false)
            ->where('draft', false)
            ->orderBy($orderBy, $sort);

        if ($paginate) {
            return $query->paginate($paginate)
                ->withQueryString()
                ->through(fn($post) => $this->formatPost($post, $formate));
        }

        if ($limit) {
            return $query->limit($limit)
                ->get()
                ->map(fn($post) => $this->formatPost($post, $formate));
        }

        return $query->get()
            ->map(fn($post) => $this->formatPost($post, $formate));
    }

    public function activePosts(
        $searchPhrase = null,
        $categoryId = null,
        $userId = null,
        $formate = 'short',
        $paginate = null,
        $limit = null,
        $orderBy = 'created_at',
        $sort = 'desc',
        $relatedToPost = null
    ) {
        $conditions = [];

        if ($searchPhrase) {
            $conditions[] = function ($query) use ($searchPhrase) {
                $query->where('content', 'like', "%{$searchPhrase}%");
            };
        }

        if ($userId) {
            $conditions[] = function ($query) use ($userId) {
                $query->where('user_id', $userId);
            };
        }

        if ($categoryId) {
            $conditions[] = function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($query) use ($categoryId) {
                    $query->where('categories.id', $categoryId);
                });
            };
        }

        if ($relatedToPost) {
            $authorId = $relatedToPost['authorId'];
            $categoryIds = $relatedToPost['categoryIds'];
            $postId = $relatedToPost['postId'];

            $conditions[] = function ($query) use ($authorId, $categoryIds, $postId) {
                $query->where('id', !$postId)
                    ->whereHas('author', function ($query) use ($authorId) {
                        $query->where('deleted', false)->where('user_id', $authorId);
                    })->orWhereHas('categories', function ($query) use ($categoryIds) {
                        $query->where('deleted', false)->whereIn('categories.id', $categoryIds);
                    });
            };
        }

        return $this->allPosts(
            optionWhere: $conditions,
            formate: $formate,
            paginate: $paginate,
            limit: $limit,
            orderBy: $orderBy,
            sort: $sort
        );
    }

    public function createPost($user = null, $postAttributes = null)
    {
        $post = $user->posts()->create([
            'title' => $postAttributes['title'],
            'content' => $postAttributes['content'],
            'image' => $postAttributes['image'] ? $postAttributes['image']->store('images', 'public') : null,
            'draft' => $postAttributes['draft'] ?? false,
        ]);

        $postAttributes['category'] ? CategoryPost::create([
            'post' => $post->id,
            'category' => $postAttributes['category'],
        ]) : null;

        return $post;
    }

    public function incrementViews(Request $request, Post $post)
    {

        $authUser = $request->user();
        if (!$authUser || (!$authUser->is_admin && $authUser->id !== $post->author->id)) $post->increment('views');
    }

    public function relatedPosts(Post $post)
    {

        $authorId = $post['authorId'];
        $categoryIds = collect($post['categories'])->pluck('id');

        return $this->activePosts(relatedToPost: ['authorId' => $authorId, 'categoryIds' => $categoryIds, 'postId' => $post->id], limit: 4);
    }
}
