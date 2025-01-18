<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{

    public function allCategories()
    {
        return Category::all();
    }

    public function createCategory($categoryAttributes)
    {
        return Category::create($categoryAttributes);
    }

    public function getCategories($deleted = false)
    {
        return Category::where('deleted', $deleted)->orderBy('created_at', 'desc')->get();
    }

    public function updateCategory(Category $category, $categoryAttributes = null)
    {
        return  $category->update($categoryAttributes);
    }

    public function deleteCategory(Category $category)
    {
        return $category->update(['deleted' => true]);
    }

    public function restoreCategory(Category $category)
    {
        return $category->update(['deleted' => false]);
    }
}
