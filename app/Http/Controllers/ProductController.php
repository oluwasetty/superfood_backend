<?php

namespace App\Http\Controllers;

use App\Http\Resources\Resource as ProductResource;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Product;
use App\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products = Product::orderBy('created_at', 'desc')->with('category:id,name')->paginate(20);
        $categories = Category::all();
        return ProductResource::collection($products)->additional(['status' => true, 'categories' => $categories]);
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
        // create products
        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'description' => $request->description,
            'sellingprice' => $request->sellingprice,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
        ]);

        return (new ProductResource($product))->additional(['status' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        // update all products
        $product = Product::find($id);
        if ($product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'description' => $request->description,
            'sellingprice' => $request->sellingprice,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
        ])) {

            return (new ProductResource($product))->additional(['status' => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // deletes a product
        $product = Product::findOrFail($id);
        if ($product->delete()) {
            return (new ProductResource($product))->additional(['status' => true]);
        }
    }

    public function updateImage(Request $request, $id)
    {
        // updates images of products by name
        $file = $request->image;

        list($type, $file) = explode(';', $file);
        list(, $file) = explode(',', $file);
        $file = base64_decode($file);

        $name = str_replace(' ', '_', $request->name) . '.' . 'jpg';
        file_put_contents(public_path() . '/storage/img/products/' . $name, $file);

        $product = Product::where('name', $request->name);
        if ($product->update([
            'img_url' => '/storage/img/products/' . $name,
        ])) {
            $product = Product::findOrFail($id);
            return new ProductResource($product);
        }
    }

    public function productsInStore()
    {
        // returns all products
        $product = new Product();
        $products = $product->allInStore();
        return $products;

        // update products set img_url= replace(img_url, '%28', '(')
        // dir | rename-item -NewName {$_.name -replace "+"," "}
        // dir | rename-item -NewName {($_.name).Replace("+"," ")}
    }

    public function productsInCategory($category)
    {
        // returns all products from a particular category in a store
        $product = new Product();
        $products = $product->inCategory($category);
        return $products;
    }

    public function viewProduct($category, $name)
    {
        // returns all information about a particular product
        $product = new Product();
        return $product->viewProduct($category, $name);
    }

    public function productsSearch($search_query)
    {
        // returns search for a product by name or category
        $product = new Product();
        $products = $product->searchStore($search_query);
        return $products;
    }
}
