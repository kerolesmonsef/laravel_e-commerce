@extends('layout')

@section('title', 'Shopping Cart')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
@endsection

@section('content')

    @component('components.breadcrumbs')
        <a href="#">Home</a>
        <i class="fa fa-chevron-right breadcrumb-separator"></i>
        <span>Shopping Cart</span>
    @endcomponent

    <div class="cart-section container">
        <div>


            @if (Cart::count() > 0)

                <h2>{{ Cart::count() }} item(s) in Shopping Cart</h2>

                <div class="cart-table">
                    @foreach (Cart::content() as $item)
                        <div class="cart-table-row">
                            <div class="cart-table-row-left">
                                <a href="{{ route('product.show', ['product'=>$item->model->slug]) }}"><img
                                        src="/img/macbook-pro.png" alt="item" class="cart-table-img"></a>
                                <div class="cart-item-details">
                                    <div class="cart-table-item"><a
                                            href="{{ route('product.show', $item->model->slug) }}">{{ $item->model->name }}</a>
                                    </div>
                                    <div class="cart-table-description">{{ $item->model->details }}</div>
                                </div>
                            </div>
                            <div class="cart-table-row-right">
                                <div class="cart-table-actions">
                                    <form action="{{ route('cart.destroy',['cart'=>$item->rowId]) }}" method="POST"
                                          class="selct">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}

                                        <button type="submit " style="color: red" class="cart-options">Remove</button>
                                    </form>

                                    <form action="{{ route('cart.switchToSaveForLater',['rowID'=>$item->rowId]) }}" method="POST">
                                        {{ csrf_field() }}

                                        <button type="submit" class="cart-options">Save for Later</button>
                                    </form>
                                </div>
                                <div>
                                    <form method="POST"
                                          action="{{ route('cart.update',['product_id'=>$item->model->id,'cart'=>$item->rowId]) }}"
                                          class="form-update-quantity">
                                        @method("PUT")
                                        @csrf
                                        <select class="quantity-select-qu" data-id="{{ $item->rowId }}"
                                                data-productQuantity="{{ $item->model->quantity }}" name="quantity">
                                            @for ($i = 1; $i < 5 + 1 ; $i++)
                                                <option {{ $item->qty == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </form>
                                </div>
                                <div>$ {{ ($item->subtotal) }}</div>
                            </div>
                        </div> <!-- end cart-table-row -->
                    @endforeach

                </div> <!-- end cart-table -->

                @if (! session()->has('coupon'))

                    <a href="#" class="have-code">Have a Code?</a>

                    <div class="have-code-container">
                        <form action="" method="POST">
                            {{ csrf_field() }}
                            <input type="text" name="coupon_code" id="coupon_code">
                            <button type="submit" class="button button-plain">Apply</button>
                        </form>
                    </div> <!-- end have-code-container -->
                @endif

                <div class="cart-totals">
                    <div class="cart-totals-left">
                        Shipping is free because we’re awesome like that. Also because that’s additional stuff I don’t
                        feel like figuring out :).
                    </div>

                    <div class="cart-totals-right">
                        <div>
                            Subtotal <br>
                            @if (session()->has('coupon'))
                                Code ({{ session()->get('coupon')['name'] }})
                                <form action="{{ route('coupon.destroy') }}" method="POST" style="display:block">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <button type="submit" style="font-size:14px;">Remove</button>
                                </form>
                                <hr>
                                New Subtotal <br>
                            @endif
                            Tax ({{config('cart.tax')}}%)<br>
                            <span class="cart-totals-total">Total</span>
                        </div>
                        <div class="cart-totals-subtotal">
                            $ {{ (Cart::subtotal()) }} <br>
                            @if (session()->has('coupon'))
                                $ {{ ($discount) }} <br>&nbsp;<br>
                                <hr>
                                $ {{ ($newSubtotal) }} <br>
                            @endif
                            $ {{ Cart::tax() }} <br>
                            <span class="cart-totals-total">$ {{ Cart::total() }}</span>
                        </div>
                    </div>
                </div> <!-- end cart-totals -->

                <div class="cart-buttons">
                    <a href="{{ route('product.index') }}" class="button">Continue Shopping</a>
                    <a href="{{ route('checkout.index') }}" class="button-primary">Proceed to Checkout</a>
                </div>

            @else
                <h3>No items in Cart!</h3>
                <div class="spacer"></div>
                <a href="{{ route('product.index') }}" class="button">Continue Shopping</a>
                <div class="spacer"></div>

            @endif

            @if (class_exists("Cart") and Cart::instance('saveForLater')->count() > 0)

                <h2>{{ Cart::instance('saveForLater')->count() }} item(s) Saved For Later</h2>

                <div class="saved-for-later cart-table">
                    @foreach (Cart::instance('saveForLater')->content() as $item)
                        <div class="cart-table-row">
                            <div class="cart-table-row-left">
                                <a href="{{ route('product.index', $item->model->slug) }}"><img
                                        src="/img/macbook-pro.png" alt="item"
                                        class="cart-table-img"></a>
                                <div class="cart-item-details">
                                    <div class="cart-table-item"><a
                                            href="{{ route('product.index', $item->model->slug) }}">{{ $item->model->name }}</a>
                                    </div>
                                    <div class="cart-table-description">{{ $item->model->details }}</div>
                                </div>
                            </div>
                            <div class="cart-table-row-right">
                                <div class="cart-table-actions">
                                    <form action="{{ route('cart.destroy', ['cart'=>$item->rowId,'type'=>'saveForLater']) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}

                                        <button type="submit" class="cart-options" style="color: red;">Remove</button>
                                    </form>

                                    <form action="{{ route('saveForLater.switchToCart', $item->rowId) }}" method="POST">
                                        {{ csrf_field() }}

                                        <button type="submit" class="cart-options">Move to Cart</button>
                                    </form>
                                </div>

                                <div>{{ $item->model->presentPrice() }}</div>
                            </div>
                        </div> <!-- end cart-table-row -->
                    @endforeach

                </div> <!-- end saved-for-later -->

            @else

                <h3>You have no items Saved for Later.</h3>

            @endif

        </div>

    </div> <!-- end cart-section -->

    @include('partials.might-like')


@endsection

@section('extra-js')
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.quantity-select-qu').on('change', function () {
                $(this).parent('.form-update-quantity').submit()
            });
        });
    </script>

    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>
@endsection
