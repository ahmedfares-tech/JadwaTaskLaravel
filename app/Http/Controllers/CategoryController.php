<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $category = Category::orderBy('id', 'desc')->withCount('products')->with('parent');
        if ($request->q) {
            $category->where('name', 'LIKE', '%' . $request->q . '%');
        }
        $category->with('childs');
        return response()->json([
            'success' => true,
            'categories' => $category->paginate($request->limit ?? 20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {

        $image = $request->file('image');
        $path = 'public/categories/' . time() . rand(0, 100) . '.' . $image->getClientOriginalExtension();
        $make = Image::make($image->getRealPath());
        Storage::put($path, $make);
        $url = Storage::url($path);
        Category::create([
            'name' => $request->name,
            'parent_id' => $request->category ?? null,
            'image' => $url
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Category created',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
