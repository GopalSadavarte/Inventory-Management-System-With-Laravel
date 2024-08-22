<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>@stack('title',"Billing System")</title>
        <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    </head>
    <body class="user-select-none">
        <nav class="navbar mx-2 bg-light row col-12 w-auto">
            <div class="nav-heading col-2" style="width: 15%">
                <h1 class="heading fs-3 p-0 m-0 text-danger">
                    Billing System
                </h1>
            </div>
            <div class="nav-links col-9 p-0">
                <ul class="mt-3 d-flex">
                    <li class="fs-5 col-1">
                        <a href="/" class="text-dark main-menu">Home</a>
                    </li>
                    <li class="fs-6 col-1">
                        <a class="text-dark main-menu fs-5">Purchase</a>
                        <ul class="sub-menu">
                            <li class="fs-6">
                                <a href="{{route('product.index')}}" class="text-dark"
                                    >Item Master</a
                                >
                            </li>
                            <li class="fs-6">
                                <a href="{{route('purchase.index')}}" class="text-dark">Purchase Entry</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('purchaseReturn.index')}}" class="text-dark">Purchase Return Entry</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('purchaseReport')}}" class="text-dark">Purchase Report</a>
                            </li>
                        </ul>
                    </li>
                    <li class="fs-6 col-1 text-center">
                        <a class="text-dark main-menu fs-5">Sale</a>
                        <ul class="sub-menu">
                            <li class="fs-6">
                                <a href="{{route('bill.index')}}" class="text-dark">Sale Bill</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('sale.index')}}" class="text-dark">Sale Report</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('group.index')}}" class="text-dark">Item Groups</a>
                            </li>
                        </ul>
                    </li>
                    <li class="fs-6 col-1">
                        <a class="text-dark main-menu fs-5">Stock</a>
                        <ul class="sub-menu">
                            <li class="fs-6">
                                <a href="{{route('stock.index')}}" class="text-dark"
                                    >Stock Entry</a
                                >
                            </li>
                            <li class="fs-6">
                                <a href="{{route('getAvailable')}}" class="text-dark">Available</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('getRequired')}}" class="text-dark">Require</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('getExpired')}}" class="text-dark">Expired</a>
                            </li>
                        </ul>
                    </li>
                    <li class="fs-6 col-1">
                        <a class="text-dark main-menu fs-5">Expiry</a>
                        <ul class="sub-menu">
                            <li class="fs-6">
                                <a href="{{route('expiry.index')}}" class="text-dark">Expiry Entry</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('weeklyExpiry')}}" class="text-dark">Weekly</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('monthlyExpiry')}}" class="text-dark">Monthly</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('expiryReturnReport')}}" class="text-dark">Exp. Return Report</a>
                            </li>
                            <li class="fs-6">
                                <a href="{{route('yearlyExpiry')}}" class="text-dark">Yearly</a>
                            </li>
                        </ul>
                    </li>
                    <li class="fs-6 col-1">
                        <a class="text-dark main-menu fs-5">Inventory</a>
                        <ul class="sub-menu">
                            <li class="fs-6">
                                <a href="#" class="text-dark"
                                    >Item Master</a
                                >
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark">Bill Maker</a>
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark">Home</a>
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark"
                                    >Item Master</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="fs-6 col-1 text-center">
                        <a class="text-dark main-menu fs-5">Utility</a>
                        <ul class="sub-menu">
                            <li class="fs-6">
                                <a href="#" class="text-dark"
                                    >Item Master</a
                                >
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark">Bill Maker</a>
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark">Home</a>
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark"
                                    >Item Master</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="fs-6 col-1 m-0">
                        <a class="text-dark main-menu fs-5">Setting</a>
                        <ul class="sub-menu">
                            <li class="fs-6">
                                <a href="#" class="text-dark"
                                    >Item Master</a
                                >
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark">Bill Maker</a>
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark">Home</a>
                            </li>
                            <li class="fs-6">
                                <a href="#" class="text-dark"
                                    >Item Master</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="fs-6 col-3 m-0">
                        <a href="#" class="text-danger fs-5 main-menu logout">Back-up & Log-out</a>
                    </li>
                </ul>
            </div>
            <div class="search-bar d-flex col-1">
                <button class="btn btn-info mx-1">help</button>
            </div>
        </nav>
        <div class="main-section">@yield('main-section')</div>
        @yield('customer-data') @yield('product-data') @yield('product_sub_info')
        @yield('bottom-script-section')
    </body>
</html>
