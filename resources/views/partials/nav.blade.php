<header>
    <div class="top-nav container">
        <div class="top-nav-left">
            <div class="logo"><a href="/">Ecommerce</a></div>


            {{ menu('main', 'partials.menus.main') }}

        </div>
        <div class="top-nav-right">

            @include('partials.menus.main-right')

        </div>
    </div> <!-- end top-nav -->
</header>
