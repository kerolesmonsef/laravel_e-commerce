@extends('layout')

@section('title', 'Products')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
@endsection

@section('content')

    @component('components.breadcrumbs')
        <a href="/">Home</a>
        <i class="fa fa-chevron-right breadcrumb-separator"></i>
        <span>Shop</span>
    @endcomponent


    <div class="products-section container">
        <div class="sidebar">
            <h3>By Category</h3>
            <ul>
                <li class="{{ !request('category') ? "active" : "" }}">
                    <a href="{{ route('product.index') }}">All Categories</a>
                </li>
                @foreach ($categories as $category)
                    <li class="{{ setActiveCategory($category->slug) }}">
                        <a href="{{ route('product.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div> <!-- end sidebar -->
        <div>
            <div class="products-header">
                <h1 class="stylish-heading">{{ $selectedCategory->name ?? "All Categories" }}</h1>
                <div>
                    <strong>Price: </strong>
                    <a href="{{ route('product.index', ['category'=> request('category'), 'sort' => 'low_high']) }}">Low
                        to High</a> |
                    <a href="{{ route('product.index', ['category'=> request('category'), 'sort' => 'high_low']) }}">High
                        to Low</a>

                </div>
            </div>

            <div class="products text-center">
                @forelse ($products as $product)
                    <div class="product">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ asset('img/'.$product->image) }}" alt="product"></a>
                        <a href="{{ route('product.show', $product->slug) }}">
                            <div class="product-name">{{ $product->name }}</div>
                        </a>
                        <div class="product-price">{{ $product->presentPrice() }}</div>
                    </div>
                @empty
                    <div style="text-align: left">No items found</div>
                @endforelse
            </div> <!-- end products -->

            <div class="spacer"></div>
            {{ $products->appends(request()->input())->links() }}
        </div>
    </div>

@endsection

@section('extra-js')
    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>
@endsection
