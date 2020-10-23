<header>
    <div class="top-nav container">
      <div class="top-nav-left">
          <div class="logo"><a href="/">Ecommerce</a></div>
          <ul>
              <li>
                  <a href="{{ route('product.index') }}">
                      Shop
                  </a>
              </li>
          </ul>
          @if (! (request()->is('checkout') || request()->is('guestCheckout')))
{{--          {{ menu('main', 'partials.menus.main') }}--}}
          @endif
      </div>
      <div class="top-nav-right">
          @if (! (request()->is('checkout') || request()->is('guestCheckout')))
          @include('partials.menus.main-right')
          @endif
      </div>
    </div> <!-- end top-nav -->
</header>