@extends('template') @push('title') Yearly Report @endpush
@section('main-section')
<div class="container" id="yearlyExpContainer">
    @session('exception')
        <x-alert id="exception" message={!!session('exception')!!}/>
    @endsession
    <div class="content">
        <div class="heading row d-flex col-12 my-2">
            <h4 class="heading text-success text-uppercase col-3 my-auto">
                <u>Yearly Expiry Report</u>
            </h4>
            <div class="search-info col-9 my-2 d-flex">
                <label
                    for="fromDate"
                    class="form-label w-auto mx-1 fs-5 my-auto"
                    >From Year:</label
                >
                <input
                    type="date"
                    name="fromDate"
                    id="fromDate"
                    class="form-control w-25 mx-1"
                />
                <label for="toDate" class="form-label w-auto fs-5 mx-1 my-auto"
                    >To Year:</label
                >
                <input
                    type="date"
                    name="toDate"
                    id="toDate"
                    class="form-control w-25 mx-1"
                />
                <button class="btn btn-success col-1 mx-1" id="search-btn">Search</button>
                <button class="btn btn-danger col-1" id="clear-btn">Clear</button>
            </div>
            <hr />
        </div>
        <div class="content">
            @if ($products->count()>0)
                <div class="product-info">
                    @php
                        $arr=[];$i=1;
                    @endphp
                    @foreach ($products as $product)
                        @isset($product->EXP)
                            @php
                                $year=substr($product->EXP,0,4);
                            @endphp
                            @if (!in_array($year,$arr))
                                @php
                                    array_push($arr,$year)
                                @endphp
                                <div class="head d-flex col-12">
                                    <h4 class="heading text-success text-uppercase col-3 my-auto">
                                        <strong>Year</strong> : {{$year}}
                                    </h4>
                                    <span class="my-auto fs-4 col-2 text-dark">Get More &rArr;</span>
                                    <button class="btn btn-warning col-1 my-auto text-white yearlyExploreMoreBtn">&hArr;</button>
                                </div>
                                <hr class="col-6">
                                <div class="product-data d-none">
                                    <table class="table table-striped table-bordered block-product-table">
                                        <tr class="table-row">
                                            <th>Sr.No.</th>
                                            <th>Product ID</th>
                                            <th>Product Name</th>
                                            <th>Group Name</th>
                                            <th>Sub Group Name</th>
                                            <th>More</th>
                                        </tr>
                                        @php
                                            $array=[];
                                        @endphp
                                        @foreach ($products as $prod)
                                            @isset($prod->EXP)
                                                @if (!in_array($prod->product->id,$array) && substr($prod->EXP,0,4)==$year)
                                                    @php
                                                        array_push($array,$prod->product->id)
                                                    @endphp
                                                    <tr class="product-row">
                                                        <td>{{$i++}}</td>
                                                        <td>{{$prod->product->product_id}}</td>
                                                        <td>{{$prod->product->product_name}}</td>
                                                        <td>{{$prod->product->group->group_name}}</td>
                                                        <td>{{$prod->product->subgroup->sub_group_name}}</td>
                                                        <td><button class="btn btn-primary exploreMoreProducts">&hArr;</button></td>
                                                    </tr>
                                                    <tr class="d-none">
                                                        <td colspan="6">
                                                            <table class="table table-striped table-bordered block-inventory-table">
                                                                <tr>
                                                                    <th>Sr.No.</th>
                                                                    <th>Quantity</th>
                                                                    <th>Rate</th>
                                                                    <th>MRP</th>
                                                                    <th>GST</th>
                                                                    <th>MFD</th>
                                                                    <th>EXP</th>
                                                                </tr>
                                                                @php
                                                                    $j=1;
                                                                @endphp
                                                                @foreach ($products as $item)
                                                                    @isset($item->EXP)
                                                                        @if (substr($item->EXP,0,4)==$year && $item->product_id == $prod->product_id)
                                                                            <tr class="inventory-row">
                                                                                <td>{{$j++}}</td>
                                                                                <td>{{$item->current_quantity}}</td>
                                                                                <td>{{$item->sale_rate}}</td>
                                                                                <td>{{$item->MRP}}</td>
                                                                                <td>{{$item->GST}}</td>
                                                                                <td>{{$item->MFD}}</td>
                                                                                <td class="expiry-dates">{{$item->EXP}}</td>
                                                                            </tr>
                                                                        @endif
                                                                    @endisset
                                                                @endforeach
                                                            </table>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endisset
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                        @endisset
                    @endforeach
                </div>
                <div class="buttons w-50 d-flex text-center">
                    <div class="mx-auto my-3 d-flex">
                        <a href="#" class="btn btn-primary col-5">Print</a>
                        <a href="{{route('expiry.index')}}" class="btn btn-success col-10 mx-1">Go to Expiry</a>
                    </div>
                </div>
            @else
                <div class="not-found">
                    <h3 class="heading text-center text-dark">
                        No Data Found!
                    </h3>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@section('bottom-script-section')
    <script type="module" src="{{asset('js/yearlyExp.js')}}"></script>
@endsection
