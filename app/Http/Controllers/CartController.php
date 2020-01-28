<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CartItem;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return all items in the cart
        $carts = Auth::user()->carts;
        return response()->json(["status" => true, 'data' => $carts]);
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
    public function store(Request $request)
    {
        // Add to cart
        $cart = Auth::user()->addToCart($request);
        return response()->json(["status" => true, 'data' => $cart]);
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
    public function update(Request $request, $id)
    {
        // Updates the carts in session
        if ($id) :
            $carts = Auth::user()->updateCart($request, $id);
        endif;
        return response()->json(["status" => true, 'data' => $carts]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Remove an item from the cart
        $cartitem = CartItem::find($id);
        Auth::user()->updateOnCartDelete($cartitem);
        if ($cartitem->delete()) :
            $carts = Auth::user()->carts;
        endif;

        return response()->json(["status" => true, 'data' => $carts]);
    }

    /**
     * Stores cart with cookie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function clearCart($id)
    {
        $carts = Auth::user()->clearCart($id);
        return response()->json(["status" => true, 'data' => $carts]);
    }
}
