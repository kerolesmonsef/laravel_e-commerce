<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        Cart::instance('saveForLater')->destroy();
        $mightAlsoLike = Product::query()
            ->MightAlsoLike()
            ->take(4)
            ->get();

        return view('cart', [
            'mightAlsoLike' => $mightAlsoLike,
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $product = Product::findOrFail(request('product_id'));


        $duplicates = Cart::search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id === $product->id;
        });

        if ($duplicates->isNotEmpty()) {
            return redirect()->route('cart.index')->with('success_message', 'Item is already in your cart!');
        }
        $cart = Cart::add($product->id, $product->name, 1, $product->price)
            ->associate(Product::class);

        return redirect()
            ->route('cart.index')
            ->with('success_message', 'Item was added to your cart!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $row_id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($row_id)
    {
        $product = Product::findOrFail(\request('product_id'));

        $this->validate(request(), [
            'quantity' => 'required|numeric|between:1,5'
        ]);

        if (request('quantity') > $product->quantity) {
            return redirect()->route('cart.index')->withErrors( ['We currently do not have enough items in stock']);
        }

        Cart::update($row_id, request('quantity'));
        return redirect()->route('cart.index')->with('success_message', 'Quantity was updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = request('type', 'default');
        Cart::instance($type)->remove($id);

        return back()->with('success_message', 'Item has been removed!');
    }


    /**
     * Switch item for shopping cart to Save for Later.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switchToSaveForLater($rowID)
    {
        $item = Cart::get($rowID);

        Cart::remove($rowID);

        $duplicates = Cart::instance('saveForLater')->search(function ($cartItem, $rowId) use ($rowID) {
            dd($rowId);
            return $rowId === $rowID;
        });

        if ($duplicates->isNotEmpty()) {
            return redirect()->route('cart.index')->with('success_message', 'Item is already Saved For Later!');
        }

        Cart::instance('saveForLater')->add($item->id, $item->name, 1, $item->price)
            ->associate(Product::class);

        return redirect()->route('cart.index')->with('success_message', 'Item has been Saved For Later!');
    }

}
