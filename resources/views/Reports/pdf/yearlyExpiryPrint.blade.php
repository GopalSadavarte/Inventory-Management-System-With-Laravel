@php
    date_default_timezone_set('Asia/Kolkata')
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$report}}</title>
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
                <h1 class="heading text-dark w-100 text-center">{{$report}}</h1>
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
            <div class="content">
                @if (count($products)>0)
                    <div class="product-info">
                        @php
                            $arr=[];$i=1;$count=0;
                        @endphp
                        @foreach ($products as $product)
                            @isset($product->EXP)
                                @php
                                    $year=substr($product->EXP,0,4);
                                    $count++;
                                @endphp
                                @if (!in_array($year,$arr))
                                    @php
                                        array_push($arr,$year)
                                    @endphp
                                    <div class="head">
                                        <h4 class="heading text-success text-uppercase col-3 my-auto">
                                            <strong>Year</strong> : {{$year}}
                                        </h4>
                                    </div>
                                    <hr class="col-6">
                                    <div class="product-data">
                                        <table class="table table-striped table-bordered block-product-table">
                                            <tr class="table-row">
                                                <th>Sr.No.</th>
                                                <th>Product ID</th>
                                                <th>Product Name</th>
                                                <th>Group Name</th>
                                                <th>Sub Group Name</th>
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
                                                        </tr>
                                                        <tr>
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
                @endif
            </div>
        </div>
    </div>
</body>
</html>
