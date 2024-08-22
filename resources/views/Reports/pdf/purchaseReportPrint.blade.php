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
        .purchase-info tr th,.purchase-info tr td {
            border: 1px solid rgb(175, 175, 175);
        }
    </style>
</head>
<body>
    <div class="w-100 shadow-sm">
        <div class="content">
            <hr>
            <div class="heading">
                <h1 class="heading text-dark w-100 text-center">Purchase Report</h1>
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
                    @php $i=1;$arr=[];$count=0; @endphp
                    @foreach ($products as $product)
                        @isset($product->dealer_name)
                            @if (!in_array($product->id,$arr))
                                @php array_push($arr,$product->id) @endphp
                                <div class="dealer-info">
                                    <div class="row">
                                        <div class="col-12">
                                            <table class="table table-striped text-center">
                                                <tr>
                                                    <td>
                                                        <div class="text-center w-100">
                                                            <h6>Sr.No.</h6>
                                                            <hr>
                                                            <span>{{$i++}}</span>
                                                            <hr>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center w-100">
                                                            <h6>Dealer Name</h6>
                                                            <hr>
                                                            <span>{{$product->dealer_name}}</span>
                                                            <hr>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center w-100">
                                                            <h6>Email</h6>
                                                            <hr>
                                                            <span>{{$product->email}}</span>
                                                            <hr>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center w-100">
                                                            <h6>Contact</h6>
                                                            <hr>
                                                            <span>{{$product->contact}}</span>
                                                            <hr>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center w-100">
                                                            <h6>GST No.</h6>
                                                            <hr>
                                                            <span>{{$product->GST_no}}</span>
                                                            <hr>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="purchase-info">
                                    <table class="table table-bordered mx-2 px-2 table-striped">
                                        <tr class="text-center">
                                            <th>Sr.No.</th>
                                            <th>Product Name</th>
                                            <th>Rate</th>
                                            <th>MRP</th>
                                            <th>Qty.</th>
                                            <th>GST</th>
                                            <th>With GST</th>
                                            <th>MFD</th>
                                            <th>EXP</th>
                                            <th>Pur.Date</th>
                                            <th>Total</th>
                                        </tr>
                                        @php
                                            $j=1;
                                            $sum=0;
                                        @endphp
                                        @foreach ($products as $data)
                                            @if ($data->id == $product->id)
                                                @foreach ($data->purchase_entry as $item)
                                                    @foreach ($allProducts as $eachProduct)
                                                        @if ($eachProduct->id == $item->purchase_product_id)
                                                            @php
                                                                $productName=$eachProduct->product_name
                                                            @endphp
                                                            @break
                                                        @endif
                                                    @endforeach
                                                    <tr class="text-center">
                                                        @php
                                                            $withGST=($item->purchase_rate + (($item->purchase_rate*$item->GST)/100));
                                                            $subTotal=$withGST*$item->addedQuantity;
                                                            $sum=$sum+$subTotal;
                                                        @endphp
                                                        <td>{{$j++}}</td>
                                                        <td>{{$productName}}</td>
                                                        <td>{{$item->purchase_rate}}</td>
                                                        <td>{{$item->MRP}}</td>
                                                        <td>{{$item->addedQuantity}}</td>
                                                        <td>{{$item->GST.'%'}}</td>
                                                        <td>{{$withGST}}</td>
                                                        <td>{{$item->productMFD}}</td>
                                                        <td>{{$item->productEXP}}</td>
                                                        <td>{{substr($item->created_at,0,10)}}</td>
                                                        <td>{{$subTotal}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                        <tr>
                                            <td colspan="11"><b style="margin: 0 auto 0 0">Total Amt.: {{$sum."/-"}}</b></td>
                                        </tr>
                                    </table>
                                    <hr>
                                </div>
                            @endif
                            @else
                              @php
                                  $count++;
                              @endphp
                        @endisset
                    @endforeach
                </div>
                @if ($count>0)
                    <div class="info purchase-info">
                        <div class="heading w-100 text-center">
                            <h2 class="text-center">Other Dealers</h2>
                        </div>
                        <table class="table table-striped table-bordered">
                            <tr class="table-row text-center">
                                <th>Sr.No.</th>
                                <th>Product Name</th>
                                <th>Qty.</th>
                                <th>Rate</th>
                                <th>MRP</th>
                                <th>GST</th>
                                <th>With GST</th>
                                <th>MFD</th>
                                <th>EXP</th>
                                <th>Pur.Date</th>
                                <th>Total</th>
                            </tr>
                            @php
                                $j=1;
                                $sum=0;
                            @endphp
                            @foreach ($products as $prod)
                                @isset($prod->product)
                                    @foreach ($prod->product as $item)
                                        @php
                                            $withGST=($item->pivot->purchase_rate + (($item->pivot->purchase_rate * $item->pivot->GST)/100));
                                            $total=$withGST*$item->pivot->addedQuantity;
                                            $sum=$sum+$total;
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{$j++}}</td>
                                            <td>{{$item->product_name}}</td>
                                            <td>{{$item->pivot->addedQuantity}}</td>
                                            <td>{{$item->pivot->purchase_rate}}</td>
                                            <td>{{$item->pivot->MRP}}</td>
                                            <td>{{$item->pivot->GST.'%'}}</td>
                                            <td>{{$withGST}}</td>
                                            <td>{{$item->pivot->productMFD}}</td>
                                            <td>{{$item->pivot->productEXP}}</td>
                                            <td>{{substr($prod->created_at,0,10)}}</td>
                                            <td>{{$total}}</td>
                                        </tr>
                                    @endforeach
                                @endisset
                            @endforeach
                            <tr>
                                <td colspan="11"><b>Total Amt.: {{$sum.'/-'}}</b></td>
                            </tr>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
