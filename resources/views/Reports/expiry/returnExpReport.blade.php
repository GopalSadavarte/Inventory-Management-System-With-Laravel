@extends('template')
@push('title')
    Expiry Return Report
@endpush
@section('main-section')
    <div class="container py-2 px-3">
        @session('dataException')
           <x-alert id="exception" message="{!!session('dataException')!!}" />
        @endsession
        <div class="report w-100 p-0 m-0">
            <div class="heading my-3 text-center d-flex col-10">
                <h4 class="heading text-uppercase text-success col-4">
                    <u>Expiry Return Report</u>
                </h4>
                <div class="mx-2 col-10 d-flex">
                    <label for="search-date1" class="col-1 fs-5 my-auto">From:</label>
                    <input type="date" id="search-date1" class="form-control">
                    <label for="search-date2" class="my-auto fs-5 px-2">To:</label>
                    <input type="date" id="search-date2" class="form-control">
                    <button class="btn btn-success col-1 px-1 mx-2" id="searchButton">search</button>
                    <button class="btn btn-danger col-1 mx-1" id="clear-btn">clear</button>
                </div>
            </div>
            @if (!empty($products) && count($products)>0)
                <div class="report-content">
                    <div class="info overflow-scroll">
                        <table class="table table-striped table-bordered">
                            <tr class="table-row">
                                <th>Sr.No.</th>
                                <th>Dealer Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>GSTIN</th>
                                <th>More</th>
                            </tr>
                            @php $i=1;$arr=[];$count=0; @endphp
                            @foreach ($products as $product)
                                @isset($product->dealer)
                                    @if (!in_array($product->dealer->id,$arr))
                                        @php array_push($arr,$product->dealer->id) @endphp
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$product->dealer->dealer_name}}</td>
                                            <td>{{$product->dealer->email}}</td>
                                            <td>{{$product->dealer->contact}}</td>
                                            <td>{{$product->dealer->GST_no}}</td>
                                            <td><button class="btn btn-danger table-buttons">&hArr;</button></td>
                                        </tr>
                                        <tr class="d-none">
                                            <td colspan="6">
                                                <table class="table table-striped table-bordered mx-2 px-2">
                                                    <tr>
                                                        <th>Sr.No.</th>
                                                        <th>Product Name</th>
                                                        <th>Qty.</th>
                                                        <th>Sale Rate</th>
                                                        <th>MRP</th>
                                                        <th>GST</th>
                                                        <th>EXP</th>
                                                        <th>Exp.Date</th>
                                                    </tr>
                                                    @php
                                                        $j=1
                                                    @endphp
                                                    @foreach ($product->product as $item)
                                                        <tr>
                                                            <td>{{$j++}}</td>
                                                            <td>{{$item->product_name}}</td>
                                                            <td>{{$item->pivot->returnQuantity}}</td>
                                                            <td>{{$item->pivot->rate}}</td>
                                                            <td>{{$item->pivot->MRP}}</td>
                                                            <td>{{$item->pivot->GST}}</td>
                                                            <td>{{$item->pivot->expiry_date}}</td>
                                                            <td>{{substr($product->created_at,0,10)}}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @php
                                        $count++;
                                    @endphp
                                @endisset
                            @endforeach
                        </table>
                    </div>
                    @if ($count > 0)
                        <div id="other-dealer-report">
                            <div class="content">
                                <div class="heading">
                                    <h5 class="heading text-success text-uppercase col-4 mx-3">
                                        <u>Other Dealers</u>
                                    </h5>
                                </div>
                                <div class="purchase-info">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Product Name</th>
                                            <th>Qty.</th>
                                            <th>Sale Rate</th>
                                            <th>MRP</th>
                                            <th>GST</th>
                                            <th>EXP</th>
                                            <th>Exp.Date</th>
                                        </tr>
                                        @php
                                            $j=1;
                                        @endphp
                                        @foreach ($products as $product)
                                            @unless($product->dealer)
                                                @foreach ($product->product as $item)
                                                    <tr>
                                                        <td>{{$j++}}</td>
                                                        <td>{{$item->product_name}}</td>
                                                        <td>{{$item->pivot->returnQuantity}}</td>
                                                        <td>{{$item->pivot->rate}}</td>
                                                        <td>{{$item->pivot->MRP}}</td>
                                                        <td>{{$item->pivot->GST}}</td>
                                                        <td>{{$item->pivot->expiry_date}}</td>
                                                        <td>{{substr($product->created_at,0,10)}}</td>
                                                    </tr>
                                                @endforeach
                                            @endunless
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="buttons bg-light g-2 w-100 text-center mx-auto my-0">
                        <div class="w-25 mx-auto d-flex">
                            <a href="#" id="printButton" class="btn btn-primary w-50">Print</a>
                            <a href="{{route('expiry.index')}}" class="btn btn-success mx-2 w-50">Back</a>
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
@endsection
@section('bottom-script-section')
<script type="module" src="{{asset('js/purchaseReport.js')}}"></script>
@endsection
