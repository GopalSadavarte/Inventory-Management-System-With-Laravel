<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Report</title>
    <style>{{$css[0]}}</style>
    <style>{{$css[1]}}</style>
    <style>
        .bill-info tr th,.bill-info tr td{
            border: 1px solid rgb(172, 169, 169);
        }
    </style>
</head>
<body>
    <div class="w-100 shadow-sm">
        <div class="content">
            <hr>
            <div class="heading">
                <h1 class="heading text-dark w-100 text-center">Sale Report</h1>
            </div>
            <hr>
            <div class="firm-info my-2 mx-3">
                <table>
                    <tr>
                        <td class="col-8">
                            <div class="info fs-5">
                                <b>Firm Name:</b>
                                <span>M.T.Traders</span>
                            </div>
                            <div class="fs-5">
                                <b>GST No.:</b>
                                <span>GSDFT1234GF23</span>
                            </div>
                            <div class="fs-5">
                                <b>Address:</b>
                                <span>Saraswati colony,ward no.7,Shrirampur,Ahmednagar,maharashtra.</span>
                            </div>
                        </td>
                        <td class="col-4 px-4">
                            <div class="fs-5">
                                <b>Date</b>
                                <span>{{date('d-m-Y')}}</span>
                            </div>
                            <div class="fs-5">
                                <b>Time</b>
                                <span>{{date('h:i a')}}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <hr>
            <div class="product-info">
                <div class="info">
                    <div class="sale-data">
                @php
                    $months=[];$count=0;
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
                            <div class="heading">
                                <h4 class="heading text-dark col-2 my-auto">{{$monthWise}}</h4>
                            </div>
                            <hr class="col-6">
                            <div class="table-container">
                                <table class="table table-striped table-bordered">
                                    <tr class="table-row">
                                        <th>Sr.No.</th>
                                        <th>Customer Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Pending Amt.</th>
                                    </tr>
                                    @php
                                        $cust=[];$i=1;
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
                                                </tr>
                                                <br>
                                                <tr>
                                                    <td colspan="6" class="bill-info">
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
                                @if($count>0)
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
                </div>
            </div>
        </div>
    </div>
</body>
</html>
