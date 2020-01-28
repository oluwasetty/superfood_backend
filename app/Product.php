<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\Resource as ProductResource;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use DB;

class Product extends Model
{
    use SoftDeletes;
    //
    protected $table = 'products';
    protected $fillable = [
        'name', 'sku', 'description', 'sellingprice', 'quantity', 'category_id',
    ];

    protected $hidden = ['deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function cartitems()
    {
        return $this->belongsTo('App\CartItem');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function promo()
    {
        return $this->belongsTo('App\Promo')->where('end_date', '>', \Carbon\Carbon::yesterday());
    }

    public function allInStore()
    {
        // fetch products from store
        $top = Product::where('quantity', '>', 5)->with('category:id,name')->limit(10)->get();
        // $beverages = Product::where(['category' => 'beverages'])->where('quantity', '>', 5)->limit(10)->get();
        // $commodities = Product::where(['category' => 'commodities'])->where('quantity', '>', 5)->limit(10)->get();
        // $water = Product::where(['category' => 'water'])->where('quantity', '>', 5)->limit(10)->get();
        return response()->json(['data' => ['top' => $top]]);
    }

    public function inCategory($category)
    {
        // fetch products in a category
        $products = Product::where('categories.name', $category)->join('categories', 'categories.id', 'products.category_id')->select('products.*')->with('category:id,name')->paginate(10);
        return ProductResource::collection($products);
    }

    public function searchStore($search_query)
    {
        // search for products in a store using category or name
        $products = Product::join('categories', 'categories.id', 'products.category_id')->where(function ($query) use ($search_query) {
            $query->where('products.name', 'LIKE', '%' . $search_query . '%')->orWhere('categories.name', 'LIKE', '%' . $search_query . '%');
        })->select('products.*')->with('category:id,name')->paginate(10);
        return ProductResource::collection($products);
    }

    public function viewProduct($category, $name)
    {
        // returns information for a particular product
        $product = Product::join('categories', 'categories.id', 'products.category_id')->where('categories.name', $category)->where('products.name', $name)->select('products.*')->with('category:id,name')->first();
        return new ProductResource($product);
    }
}
