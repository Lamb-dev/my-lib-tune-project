<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Illuminate\Http\Request;

class BookCategoryController extends Controller
{
    public function index()
    {
        $categories = BookCategory::orderBy('cate_id', 'desc')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cate_name' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    $exists = BookCategory::whereRaw('LOWER(TRIM(cate_name)) = ?', [strtolower(trim($value))])
                        ->exists();

                    if ($exists) {
                        $fail('A category with this name already exists. Check the list before adding a new one.');
                    }
                },
            ],
        ]);

        $validated['cate_name'] = trim($validated['cate_name']);

        BookCategory::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(BookCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, BookCategory $category)
    {
        $validated = $request->validate([
            'cate_name' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($category) {
                    $exists = BookCategory::whereRaw('LOWER(TRIM(cate_name)) = ?', [strtolower(trim($value))])
                        ->where('cate_id', '!=', $category->cate_id)
                        ->exists();

                    if ($exists) {
                        $fail('Another category with this name already exists.');
                    }
                },
            ],
        ]);

        $validated['cate_name'] = trim($validated['cate_name']);

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(BookCategory $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
