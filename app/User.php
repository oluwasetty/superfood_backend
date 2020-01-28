<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends \Cartalyst\Sentinel\Users\EloquentUser implements
    JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }


    protected $appends = ['fullname'];

    public function cart()
    {
        return $this->hasOne('App\Cart', 'user_id');
    }

    public function order()
    {
        return $this->hasOne('App\Order', 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany('App\UserAddress', 'user_id');
    }

    public function getFullnameAttribute()
    {
        return strtoupper("{$this->title} {$this->first_name} {$this->last_name}");
    }

    public function getCartsAttribute()
    {
        $carts = Cart::where('user_id', $this->id)->with('cartItems')->first();
        return $carts;
    }

    public function updateOnCartDelete($cartitem)
    {
        $cart = Cart::where('user_id', $this->id)->where('id', $cartitem->cart_id)->first();
        if ($cart !== null) {
            $cart->total_price -= $cartitem->price;
            $cart->quantity -= $cartitem->quantity;
        }
        $cart->save();
        return $cart;
    }

    public function updateCart($cart, $id)
    {
        $carts = Cart::where('user_id', $this->id)->where('id', $id)->with('user')->with('cartItems')->first();
        if ($carts !== null) {
            $carts->total_price = $cart->total_price;
            $carts->quantity = $cart->quantity;
            $carts->save();
        }
        foreach ($cart['cart_items'] as $key => $cart_item) {
            $cartitem = CartItem::where('cart_id', $id)->where('product_id', $cart_item['product_id'])->first();
            if ($cartitem !== null) {
                $cartitem->price = $cart_item['price'];
                $cartitem->quantity = $cart_item['quantity'];
            }
            $cartitem->save();
        }
        return $this->carts;
    }

    public function getCartAttribute()
    {
        $cart = Cart::where('user_id', $this->id)->first();
        return $cart;
    }

    public function clearCart($id)
    {
        CartItem::where('cart_id', $id)->forceDelete();
        $carts = Cart::find($id);
        if ($carts !== null) {
            $carts->total_price = 0;
            $carts->quantity = 0;
            $carts->save();
        }
        return $this->carts;
    }

    public function addToCart($request)
    {
        // adds products to cart
        $user_id = $this->id;
        $usercart = Cart::where('user_id', $user_id)->first();
        if ($usercart === null) {
            $cart = new Cart();
            $cart->user_id = $user_id;
            $cart->total_price = $request->price;
            $cart->quantity = $request->quantity;
        } else {
            $cart = $usercart;
            $cart->total_price += $request->price;
            $cart->quantity += $request->quantity;
        }
        $cart->save();
        $cart_id = $cart->id;
        $cart_item = CartItem::where('cart_id', $cart_id)->where('product_id', $request->product['id'])->first();
        if ($cart_item === null) {
            $cartitem = new CartItem();
            $cartitem->cart_id = $cart_id;
            $cartitem->unit_price += $request->unit_price;
            $cartitem->price = $request->price;
            $cartitem->product_id = $request->product['id'];
            $cartitem->quantity = $request->quantity;
        } else {
            $cartitem = $cart_item;
            $cartitem->price += $request->price;
            $cartitem->quantity += $request->quantity;
        }
        $cartitem->save();
        return $this->cart;
    }

    public function placeOrder($request)
    {
        // places order for customers
        if ($request->delivery['method'] === 'delivery' && is_null($request->delivery['id'])) {
            // saves users delivery address
            $delivery_id = UserAddress::insertGetId([
                'user_id' => $this->id,
                'city' => $request->delivery['city'],
                'address' => $request->delivery['address'],
                'landmark' => $request->delivery['landmark'],
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
            ]);
        } else {
            $delivery_id = $request->delivery['id'];
        }
        $order_id = Order::insertGetId([
            'user_id' => $this->id,
            'delivery_method' => $request->delivery['method'],
            'order_total' => $request->order_total,
            'balance' => $request->order_total,
            'cart_subtotal' => $request->cart_subtotal,
            'store' => $request->store,
            'payment_method' => $request->payment['method'],
            'address_id' => $delivery_id,
            'delivery_charge' => $request->delivery['charge'],
            'delivery_hour' => $request->delivery['hour'],
            'comment' => $request->comment,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        ]);
        foreach ($request->order_items as $orderitem) {
            OrderItem::create([
                'order_id' => $order_id,
                'product_id' => $orderitem['product']['id'],
                'quantity' => $orderitem['quantity'],
                'unit_price' => $orderitem['unit_price'],
                'price' => $orderitem['price']
            ]);
        }
        // if (!is_null($request->cart_id)) {
        //     CartItem::where('cart_id', $request->cart_id)->forceDelete();
        //     Cart::find($request->cart_id)->forceDelete();
        // }
        // clears cart
        session()->forget('cart');
        $url = '/payment?order=' . $order_id;

        // sends email to user
        $email = $this->email;
        $order = Order::where('id', $order_id)->with('user:id,first_name,last_name,title', 'orderitems')->first();
        $data = array(
            "order" => $order,
            "user" => $this,
            "url" => $url
        );

        Mail::send('mail.invoice', $data, function ($message) use ($email) {
            $message->to($email, $this->fullname)
                ->subject('Invoice from Market Square');
            $message->from('noreply@marketsquareng.website', 'Market Square');
        });
        return response()->json(["success" => true, "message" => "Order placed successfully", "redirect_url" => $url]);
    }
}
