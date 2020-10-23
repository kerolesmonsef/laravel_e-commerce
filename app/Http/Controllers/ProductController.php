<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index()
    {
        $pagination_count = 9;
        $this->validate(\request(), [
            'category' => ['bail', 'nullable', 'exists:category,slug'],
            'sort' => ['bail', 'nullable', 'in:low_high,high_low'],
        ]);

        $categories = Category::all();

        if (request('category')) {
            $products = Product::with('categories')->whereHas('categories', function ($query) {
                $query->where('slug', request('category'));
            });
            $selectedCategory = $categories->where('slug', request('category'))->first();

        } else {
            $products = Product::query();//where('featured', true);
            $selectedCategory = 'Featured';
        }

        if (request('sort') == 'low_high') {
            $products = $products->orderBy('price');
        } elseif (request('sort') == 'high_low') {
            $products = $products->orderBy('price', 'desc');
        }
//        dd($products->dd());

        $categories = Category::all();
        $products = $products->paginate($pagination_count);

        return view('products')->with([
            'products' => $products,
            'categories' => $categories,
            "selectedCategory" => $selectedCategory,
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
