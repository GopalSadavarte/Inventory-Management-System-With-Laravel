@extends('template') @push('title') Sale Report @endpush
@section('main-section')
<div class="container my-4">
    @session('exception')
        <x-alert id="exception" message="{!!session('exception')!!}"/>
    @endsession
    <div class="content">
        <div class="head-content d-flex mx-2">
            <h2 class="heading text-success text-uppercase col-3 my-auto">
                <u>Sale Report</u>
            </h2>
            <div class="search-data d-flex col-9 my-2 mx-3">
                <label for="fromDate" class="form-label my-auto col-1 fs-4">From :</label>
                <input
                    type="date"
                    name="fromDate"
                    id="fromDate"
                    class="form-control mx-1"
                />
                <label for="toDate" class="form-label col-1 fs-4 my-auto">To :</label>
                <input
                    type="date"
                    name="toDate"
                    id="toDate"
                    class="form-control mx-1"
                />
                <button class="btn btn-success col-1 mx-1" id="search-report">Search</button>
                <button class="btn btn-danger col-1 mx-1" id="clear-btn-1">clear</button>
            </div>
        </div>
        <hr />
        <div class="head-content d-flex mx-2">
            <h4 class="heading text-success text-uppercase col-3 my-auto">
                <u>Get Bill </u>
            </h4>
            <div class="search-data d-flex col-9 my-2 mx-3">
                <label for="date" class="form-label my-auto">Date:</label>
                <input
                    type="date"
                    name="date"
                    id="date"
                    class="form-control mx-1"
                />
                <label for="billNo" class="col-1 my-auto">Bill No.:</label>
                <input
                    type="number"
                    name="billNo"
                    id="billNo"
                    class="form-control mx-1"
                />
                <button class="btn btn-success col-1 mx-1" id="search-button">Search</button>
                <button class="btn btn-danger col-1 mx-1" id="clear-btn">clear</button>
            </div>
        </div>
        <hr>
        <div class="info">
            @if ($bills != null && !empty($bills))
                <div class="sale-data">
                    @php
                        $months=[];
                    @endphp
                    @foreach ($bills as $item)
                        @php
                            $date=preg_split('/[\-]/',substr($item->created_at,0,7));
                            $monthWise=$date[1].'-'.$date[0];
                        @endphp
                        @if (!in_array($monthWise,$months))
                            @php
                                array_push($months,$monthWise)
                            @endphp
                            <div class="contant">
                                <div class="heading row">
                                    <h4 class="heading text-dark col-2 my-auto">{{$monthWise}}</h4>
                                    <span class="col-3 my-auto fs-4">Get More &rArr;</span>
                                    <button class="btn btn-warning col-1 text-white monthWiseExploreBtn">&hArr;</button>
                                </div>
                                <hr class="col-6">
                                <div class="table-container d-none">
                                    <table class="table table-striped table-bordered">
                                        <tr class="table-row">
                                            <th>Sr.No.</th>
                                            <th>Customer Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Pending Amt.</th>
                                            <th>More</th>
                                        </tr>
                                        @php
                                            $cust=[];$i=1;$count=0;
                                        @endphp
                                        @foreach ($bills as $eachProd)
                                            @isset($eachProd->bill_customer->id)
                                                @if (!in_array($eachProd->bill_customer->id,$cust))
                                                    @php
                                                        array_push($cust,$eachProd->bill_customer->id)
                                                    @endphp
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>{{$eachProd->bill_customer->customer_name}}</td>
                                                        <td>{{$eachProd->bill_customer->customer_email}}</td>
                                                        <td>{{$eachProd->bill_customer->contact}}</td>
                                                        <td>{{$eachProd->bill_customer->pending_amt}}</td>
                                                        <td><button class="btn btn-primary getMoreByCustBtn">&hArr;</button></td>
                                                    </tr>
                                                    <tr class="d-none">
                                                        <td colspan="6">
                                                            <table class="table table-striped table-bordered">
                                                                <tr class="table-row">
                                                                    <th>Sr.No.</th>
                                                                    <th>Product Id</th>
                                                                    <th>Product Name</th>
                                                                    <th>Qty.</th>
                                                                    <th>MRP</th>
                                                                    <th>Rate</th>
                                                                    <th>Discount</th>
                                                                    <th>Net Amt.</th>
                                                                    <th>Total</th>
                                                                    <th>Bill No.</th>
                                                                    <th>Sale Date</th>
                                                                </tr>
                                                                @php
                                                                    $j=1;
                                                                @endphp
                                                                @foreach ($bills as $data)
                                                                    @php
                                                                        $date=preg_split('/[\-]/',substr($data->created_at,0,7));
                                                                        $dateWithMonthYear=$date[1].'-'.$date[0];
                                                                    @endphp
                                                                    @isset($data->bill_customer->id)
                                                                        @if ($dateWithMonthYear == $monthWise && $data->bill_customer->id == $eachProd->bill_customer->id)
                                                                            @foreach ($data->bill_product as $product)
                                                                                @php
                                                                                    $rate=$product->pivot->newRate;
                                                                                    $disc=$product->pivot->newDiscount;
                                                                                    $qty=$product->pivot->newQuantity;

                                                                                    $netPrice = ($rate - (($rate*$disc)/100));
                                                                                    $total=$netPrice * $qty;
                                                                                @endphp
                                                                                <tr>
                                                                                    <td>{{$j++}}</td>
                                                                                    <td>{{$product->product_id}}</td>
                                                                                    <td>{{$product->product_name}}</td>
                                                                                    <td>{{$qty}}</td>
                                                                                    <td>{{$product->pivot->newMRP}}</td>
                                                                                    <td>{{$rate}}</td>
                                                                                    <td>{{$disc}}</td>
                                                                                    <td>{{$netPrice}}</td>
                                                                                    <td>{{$total}}</td>
                                                                                    <td class="bill-number">{{$data->dayWiseBillNumber}}</td>
                                                                                    <td class="bill-date">{{substr($data->created_at,0,10)}}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                    @endisset
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
                                    @if ($count>0)
                                        <div class="other-customers">
                                            <div class="content">
                                                <div class="heading">
                                                    <h4 class="heading text-uppercase text-success">
                                                        <u>Other Customer's</u>
                                                    </h4>
                                                </div>
                                                <div class="bill-info">
                                                    <table class="table table-striped table-bordered">
                                                        <tr class="table-row">
                                                            <th>Sr.No.</th>
                                                            <th>Product Id</th>
                                                            <th>Product Name</th>
                                                            <th>Qty.</th>
                                                            <th>MRP</th>
                                                            <th>Rate</th>
                                                            <th>Discount</th>
                                                            <th>Net Amt.</th>
                                                            <th>Total</th>
                                                            <th>Bill No.</th>
                                                            <th>Sale Date</th>
                                                        </tr>
                                                        @php
                                                            $k=1;
                                                        @endphp
                                                        @foreach ($bills as $item)
                                                            @if ($item->bill_customer == null && empty($item->bill_customer))
                                                                @foreach ($item->bill_product as $prod)
                                                                    @php
                                                                        $qty=$prod->pivot->newQuantity;
                                                                        $rate=$prod->pivot->newRate;
                                                                        $disc=$prod->pivot->newDiscount;

                                                                        $netPrice=($rate - (($rate * $disc)/100));
                                                                        $total=$netPrice*$qty;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{$k++}}</td>
                                                                        <td>{{$prod->product_id}}</td>
                                                                        <td>{{$prod->product_name}}</td>
                                                                        <td>{{$qty}}</td>
                                                                        <td>{{$prod->pivot->newMRP}}</td>
                                                                        <td>{{$rate}}</td>
                                                                        <td>{{$disc}}</td>
                                                                        <td>{{$netPrice}}</td>
                                                                        <td>{{$total}}</td>
                                                                        <td class="bill-number">{{$item->dayWiseBillNumber}}</td>
                                                                        <td class="bill-date">{{substr($item->created_at,0,10)}}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <x-report-button
                    className='w-50 mx-1 d-flex gx-2 bg-light'
                    printRoute='printSaleReport'
                    goToRoute='bill.index'
                    idForPrintRoute='printBtn'
                    goto='Go to Sale Bill'
                />
            @else
                <x-report-not-found className='container'/>
            @endif
        </div>
    </div>
</div>
@endsection
@section('bottom-script-section')
    <script type="module" src="{{asset('js/saleReport.js')}}"></script>
@endsection
