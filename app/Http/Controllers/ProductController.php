<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::query()
            ->where('featured', true)
            ->take(12)
            ->inRandomOrder()
            ->get();

        return view('shop')->with([
            'products' => $products,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = Product::query()->where('slug', $slug)->firstOrFail();
        $mightAlsoLike = Product::query()
            ->where('slug', '!=', $slug)
            ->take(4)
            ->inRandomOrder()
            ->get();


        return view('product')->with([
            'product' => $product,
            'mightAlsoLike' => $mightAlsoLike,
        ]);
    }


}
