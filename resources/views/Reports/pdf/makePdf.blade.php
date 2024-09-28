@php date_default_timezone_set('Asia/Kolkata'); @endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Document</title>
        <style>
            {{$css[0]}}
        </style>
        <style>
            {{$css[1]}}
        </style>
    </head>
    <body>
        <div class="container mt-3 border rounded p-4 mb-3" style="width: 60%">
            <div class="content">
                <div class="heading-section">
                    <h1 class="heading text-center">M.T.Traders</h1>
                    <address>
                        <div
                            class="firm-address text-center"
                            style="font-weight: 500"
                        >
                            <p class="fs-5">
                                Laxminagar,Dalvi wasti, ward No.7,<br />Near
                                Morage Hospital,Shrirampur-413709
                            </p>
                            <span>Contact : 8956434711/02422222436</span><br />
                            <span>GST No.:27AERTS8769MASD</span>
                        </div>
                    </address>
                </div>
                <hr />
                <div class="payment-type text-center w-100 fs-5">
                    <b>CASH PAYMENT</b>
                </div>
                <div class="customer-info col-12">
                    <table class="w-100 table">
                        <tr>
                            <td class="col-10">
                                <div class="col-8 d-flex gx-5 mt-2 fs-6 w-100">
                        <div class="col-4 w-50">
                            <b>Date:</b>
                            <span>{{ date('d-m-Y') }}</span>
                        </div>
                        <div class="col-4 w-50">
                            <b>Time:</b>
                            <span>{{ date('h:i A') }}</span>
                        </div>
                    </div>
                            </td>
                            <td class="col-6">
                                <div class="col-5 w-50">
                            <b>Counter:</b>
                            <span>{{ $counter }}</span>
                        </div>
                        <div class="col-5 w-50 mx-2">
                            <b>Bill No.</b>
                            <span>{{ $billNumber }}</span>
                        </div>
                    </div>
                            </td>
                        </tr>
                    </table>
                    <hr />
                    <div class="col-4 w-100">
                        <b>Customer Name:</b>
                        <span>{{ $customerName }}</span>
                    </div>
                </div>
                <hr />
                <div class="bill-info w-100">
                    <table class="table">
                        <tr>
                            <th>Sr.No.</th>
                            <th>Perticular</th>
                            <th>MRP</th>
                            <th>Qty.</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                        @php $j=1;$sum=0; @endphp
                         @for ($i = 0; $i < count($pId);$i++)
                        <tr>
                            <td>{{ $j }}</td>
                            <td class="text-align-left">
                                {{ $productName[$i] }}
                            </td>
                             <td>{{ $mrp[$i] }}</td>
                            <td>{{ $qty[$i] }}</td>
                            <td>{{ $rate[$i] }}</td>
                            <td>{{ $netAmount[$i] }}</td>
                            @php $j++;$sum=$sum+$qty[$i];@endphp
                        </tr>
                        @endfor
                    </table>
                    <div class="total w-75" style="margin: 0 0 0 auto">
                        <b class="fs-5">Total Qty:</b>
                        <span class="fs-5 mx-2">{{ $sum }}</span>
                        <span class="fs-5" style="margin-left: 5rem">{{
                            'Rs. '.number_format($total)."/-"
                        }}</span>
                    </div>
                </div>
                <hr />
                <div
                    class="bill-amount border w-50 rounded my-0 mx-auto p-2 fs-4"
                >
                    <b>Bill Amount : {{ "Rs. ".number_format($total)."/-" }}</b>
                </div>
                <div class="amount-description">
                    <b>Paid Amount:</b><span>{{ number_format($paid) }}</span>
                    <b>Returned Amount:</b><span>{{ number_format($return) }}</span>
                </div>
                <div class="discount border rounded w-50 my-0 mx-auto p-2 fs-5">
                    <span
                        >Your
                        <span class="text-uppercase"
                            ><u><b>Discount : </b></u></span
                        >
                    </span>
                    <span>{{number_format($discount)}}</span>
                </div>
                <hr />
                <div class="fs-4 row col-12 text-center">
                    <span class="col-5">Thank You!!</span>
                    <span class="col-5">Visit Again!!</span>
                </div>
                <hr />
                <div class="terms-conditions mt-0">
                    <span class="text-danger">
                        <span class="fs-4">&ast;</span>
                        Conditions :
                    </span>
                    <div class="description w-100">
                        <ul>
                            <li>Product once sale,it cannot return back.</li>
                            <li>Check Products on bill counter.</li>
                            <li>Exchange Or return policy not available.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
