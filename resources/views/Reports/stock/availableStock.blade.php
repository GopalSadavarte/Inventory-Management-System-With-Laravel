@extends('template')
@push('title')
    Product Stock
@endpush
@section('main-section')
    <div class="container py-2 px-3">
        @session('dataException')
           <x-alert id="exception" message="{!!session('dataException')!!}" />
        @endsession
        <div class="report w-100 p-0 m-0">
            <div class="heading my-3 text-center d-flex col-10">
                <h4 class="heading text-uppercase text-success col-3">
                    <u>Available Stock</u>
                </h4>
                <div class="mx-2 col-10 d-flex">
                    <label for="p-id" class="col-2 my-auto">Product ID:</label>
                    <input type="search" name="p-id" id="p-id" class="form-control" placeholder="Search product ID..">
                    <label for="product-name" class="col-2 my-auto">Product Name:</label>
                    <input type="search" name="product-name" id="product-name" class="form-control" placeholder="Search product name..">
                </div>
            </div>
            <hr>
            <div class="sub-section my-4">
                @if (!empty($products) && count($products)>0)
                    <div class="report-content">
                        @php
                            $arr=[];
                        @endphp
                        @foreach ($products as $item)
                            @if (!in_array($item->product->group->group_id,$arr))
                                @php
                                    array_push($arr,$item->product->group->group_id)
                                @endphp
                                <div class="info overflow-scroll">
                                    <div class="head d-flex col-12 block-heading">
                                        <h5 class="heading text-success text-uppercase col-3 my-auto">
                                            <strong>Group</strong> : {{$item->product->group->group_name}}
                                        </h5>
                                        <span class="my-auto fs-4 col-2 text-dark">Get More &rArr;</span>
                                        <button class="btn btn-warning col-1 my-1 text-white yearlyExploreMoreBtn">&hArr;</button>
                                    </div>
                                    <hr class="col-8">
                                    <div class="product-info d-none">
                                        <table class="table table-striped table-bordered product-info-table">
                                            <tr class="table-row">
                                                <th>Sr.No.</th>
                                                <th>Product ID</th>
                                                <th>Product Name</th>
                                                <th>Sub Group</th>
                                                <th>Available Quantity</th>
                                            </tr>
                                            @php
                                                $j=1;
                                            @endphp
                                            @foreach ($products as $prod)
                                                @if ($prod->product->group->group_id==$item->product->group->group_id)
                                                    <tr>
                                                        <td>{{$j++}}</td>
                                                        <td class="product-id">{{$prod->product->product_id}}</td>
                                                        <td class="product-name">{{$prod->product->product_name}}</td>
                                                        <td>{{$prod->product->subgroup->sub_group_name}}</td>
                                                        <td>{{$prod->CQTY}}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div class="d-none w-100 text-center my-2">
                            <h4 class="heading text-center text-dark" id="notFoundText">
                                <u>No Data Found!</u>
                            </h4>
                        </div>
                        <div class="buttons bg-light g-2 w-100 text-center mx-auto my-0">
                            <div class="w-25 mx-auto d-flex">
                                <a href="{{route('availableStockPrint')}}" id="printButton" class="btn btn-primary w-50">Print</a>
                                <a href="{{route('stock.index')}}" class="btn btn-success mx-2 w-50">Back</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="reports my-2">
                        <h4 class="heading text-center text-dark">
                            No Data Found!
                        </h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('bottom-script-section')
    <script type="module" src="{{asset('js/stockReports.js')}}"></script>
@endsection
