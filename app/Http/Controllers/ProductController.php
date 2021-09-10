<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\CollectProductOrder;
use App\Models\Option;
use App\Models\Product;
use App\Models\products_options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $limit = $request->limit ?? 20;
        $products = Product::orderBy('id', $request->order)->with('category');
        if ($request->name != 'undefined') $products->where('name', 'LIKE', '%' . $request->name . '%');

        return response()->json(['success' => true, 'products' => $products->paginate($limit)]);
    }
    public function play(Request $request,  $productID)
    {
        $product = Product::where('id', $productID)->with('category')->first();
        $group = collect($product->options)->groupBy('option.name');
        return response()->json(['product' => $product, 'options' => $group]);
    }
    public function calculation(Request $request)
    {
        $total = 0;
        $product = Product::where('id', $request->productID)->with('category')->first();
        $total += ($request->qty * $product->price);
        foreach ($request->options as $key => $id) {
            $p =   products_options::where('id', $id)->first();
            $total += $p->price;
        }
        return response()->json([
            'total' => $total,
        ]);
    }
    public function cart(Request $request)
    {
        $total = 0;
        $cart = app(CartController::class)->store($request);

        $product = Product::where('id', $request->productID)->with('category')->first();
        $total += ($request->qty * $product->price);
        foreach ($request->options as $key => $id) {
            $p =   products_options::where('id', $id)->first();
            $total += $p->price;
            if ($p) {
                CollectProductOrder::create([
                    'products_options_id' => $p->id,
                    'cart_id' => $cart['cart']->id
                ]);
            }
        }
        $cart['cart']->update([
            'total' => $total,
            'qty' => $request->qty
        ]);
        return response()->json([
            'total' => $total,
            'success' => true
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
    public function store(ProductRequest $request)
    {
        $data = collect(json_decode($request->options));
        $image = $request->file('image');

        $path = 'public/products/1' . '/' . time() . rand(0, 100) . '.' . $image->getClientOriginalExtension();
        $make = Image::make($image);
        Storage::put($path, $make->encode);
        $url = Storage::url($path);
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category,
            'image' => $url,
            'description' => $request->description ?? ''
        ]);
        $this->HandleProductOption($data, $product);
        return response()->json([
            'success' => true,
            'message' => 'Product created',
        ]);
    }
    public function HandleProductOption($data, $product)
    {
        products_options::where('product_id', $product->id)->delete();
        foreach ($data->groupBy('name') as $key => $value) {
            foreach ($value as $secondKey => $secondValue) {
                $option = Option::firstOrCreate([
                    'name' => $key
                ], ['optional' => $secondValue->optional]);
                products_options::create([
                    'value' => $secondValue->value,
                    'price' => $secondValue->price,
                    'optional' => $secondValue->optional,
                    'option_id' => $option->id,
                    'product_id' => $product->id
                ]);
            }
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
        $productShow = Product::where('id', $product)->with(['category', 'options.option'])->first();
        return response()->json(['product' => $productShow]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
        $data = collect(json_decode($request->options));
        $updates = [];
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = 'public/products/1' . '/' . time() . rand(0, 100) . '.' . $image->getClientOriginalExtension();
            $make = Image::make($image);
            Storage::put($path, $make->encode());
            $url = Storage::url($path);
            $product->update(['image' => $url]);
        }
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category,
            'description' => $request->description ?? ''
        ]);
        $this->HandleProductOption($data, $product);
        return response()->json([
            'success' => true,
            'message' => 'Product updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
