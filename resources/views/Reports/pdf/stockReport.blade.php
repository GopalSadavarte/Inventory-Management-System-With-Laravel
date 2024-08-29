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
            <div class="product-info">
                <div class="info">
                    @php $i=1;$arr=[];$count=0; @endphp
                    @foreach ($products as $product)
                        @if (!in_array($product->product->group->group_id,$arr))
                            @php array_push($arr,$product->product->group->group_id) @endphp
                            <div class="group-info">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="heading text-dark">{{$product->product->group->group_name}}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="purchase-info">
                                <table class="table table-bordered mx-2 px-2 table-striped">
                                    <tr class="text-center">
                                        <th>Sr.No.</th>
                                        <th>Product Name</th>
                                        <th>Sub Group</th>
                                        <th>Qty.</th>
                                        @isset($product->EXP)
                                            <th>Expiry Date</th>
                                        @endisset
                                    </tr>
                                    @php
                                        $j=1;
                                        $sum=0;
                                    @endphp
                                    @foreach ($products as $item)
                                        @if ($item->product->group->group_id == $product->product->group->group_id)
                                            <tr class="text-center">
                                                <td>{{$j++}}</td>
                                                <td>{{$item->product->product_name}}</td>
                                                <td>{{$item->product->subGroup->sub_group_name}}</td>
                                                <td>{{$item->CQTY}}</td>
                                                @isset($item->EXP)
                                                    <td>{{$item->EXP}}</td>
                                                @endisset
                                                @php
                                                    $sum=$sum+$item->CQTY;
                                                @endphp
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="11"><b style="margin: 0 auto 0 0">Total Quantity By Group: {{$sum}}</b></td>
                                    </tr>
                                </table>
                                <hr>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>
</html>
