@extends('template')
@push('title')
    Sale Bill
@endpush
@section('main-section')
<div class="bg-light px-4 my-2 py-2 shadow rounded mx-4 h-100" id="bill-Entry">
    <div class="mt-2">
        <h3 class="heading">Sale Bill</h3>
    </div>
    <form id="customerSelectForm" method="post" action="{{route('bill.store')}}">
        @csrf
        <span id="method"></span>
        <div class="content">
            <div class="d-flex">
                <div class="row g-5 col-12">
                    <div class="d-flex col-12">
                        <div class="col-4 d-none">
                            <label for="customerId">customer ID</label>
                            <input
                                type="text"
                                name="customerId"
                                id="customerId"
                                class="form-control"
                                placeholder="Customer Name"
                            />
                        </div>
                        <div class="col-4">
                            <label for="customerName">Name</label>
                            <input
                                type="text"
                                name="customerName"
                                id="customerName"
                                class="form-control"
                                placeholder="Customer Name"
                            />
                        </div>
                        <div class="col-4 mx-2">
                            <label for="customerEmail">Email</label>
                            <input
                                type="text"
                                name="customerEmail"
                                id="customerEmail"
                                class="form-control"
                                placeholder="Customer Email"
                            />
                        </div>
                        <div class="col-3">
                            <label for="contactNumber">Contact</label>
                            <input
                                type="text"
                                name="contactNumber"
                                id="contactNumber"
                                class="form-control"
                                placeholder="Contact No."
                            />
                        </div>
                    </div>
                    <div class="d-flex col-12" style="margin-top: -4rem">
                        <div class="col-6">
                            <label for="addr">Address</label>
                            <input
                                type="text"
                                name="addr"
                                id="addr"
                                class="form-control"
                                placeholder="Street Address"
                            />
                        </div>
                        <div class="col-3 mx-2">
                            <label for="paymentType">Payment Type</label>
                            <select name="paymentType" id="paymentType" class="form-control">
                                <option value="cash">Cash</option>
                                <option value="Debit/Credit Card">
                                    Debit/Credit Card
                                </option>
                                <option value="UPI/PhonePay">
                                    UPI/PhonePay
                                </option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="pendingAmount">Pending Amount</label>
                            <input
                                type="text"
                                name="pendingAmount"
                                id="pendingAmount"
                                readonly
                                class="form-control"
                            />
                        </div>
                    </div>
                    <div class="d-flex col-12" style="margin-top: -4rem">
                        <div class="col-2">
                            <label for="counterNumber">Counter No.</label>
                            <input
                                type="number"
                                name="counterNumber"
                                id="counterNumber"
                                class="form-control"
                                value="1"
                                readonly
                            />
                        </div>
                        <div class="col-2 mx-2">
                            <label for="billNumber">Bill No.</label>
                            <input
                                type="number"
                                name="billNumber"
                                id="billNumber"
                                value="{{ $billNo }}"
                                readonly
                                class="form-control"
                            />
                            <small id="billNoError" class="text-danger"></small>
                        </div>
                        <div class="col-2 mx-2">
                            <label for="date">Date</label>
                            <input
                                type="date"
                                name="date"
                                value="{{ date('Y-m-d') }}"
                                id="date"
                                readonly
                                class="form-control"
                            />
                        </div>
                        <div class="col-2">
                            <label for="print">Print</label>
                            <select name="print" id="print" class="form-control">
                                <option value="Yes" selected>YES</option>
                                <option value="No">NO</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="buttons col-1 mt-2">
                    <button class="btn btn-warning" disabled>New</button>
                    <button class="btn btn-success" disabled>Edit</button>
                    <input type="submit" value="Save" class="btn btn-primary mt-3 w-100" id="submit" disabled>
                    <button class="btn btn-info" disabled>Delete</button>
                    <input
                        type="reset"
                        name="reset"
                        value="Cancel"
                        id="reset"
                        class="btn btn-danger mt-3"
                    />
                </div>
            </div>
        </div>
        <div class="section overflow-scroll w-100 shadow-sm px-1 py-2">
            <table class="table table-bordered table-striped" id="bill-table">
                <tr>
                    <th class="d-none">id</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>MRP</th>
                    <th>Discount</th>
                    <th>Net Amount</th>
                </tr>
            </table>
        </div>
        <div class="total w-100 d-flex mt-3">
            <div class="d-flex">
                <span class="fs-6">Previous Amt.:</span
                ><input
                    type="text"
                    name="previousBillAmount"
                    id="previousBillAmount"
                    class="form-control mx-2 fs-6 my-auto"
                    style="height: 2rem"
                    value="{{$lastBillAmount}}"
                    readonly
                />
            </div>
            <div class="d-flex">
                <span class="fs-6">Paid Amount:</span
                ><input
                    type="number"
                    name="paidAmount"
                    id="paidAmount"
                    class="form-control mx-2 fs-6 my-auto"
                    style="height: 2rem"
                />
            </div>
            <div class="d-flex">
                <span class="fs-6">Payable Amount:</span
                ><input
                    type="number"
                    name="payableAmount"
                    id="payableAmount"
                    class="form-control mx-2 fs-6 my-auto"
                    style="height: 2rem"
                    readonly
                />
            </div>
            <div class="d-flex">
                <span class="fs-6">Return Amount:</span
                ><input
                    type="text"
                    name="returnAmount"
                    id="returnAmount"
                    class="form-control mx-2 fs-6 my-auto"
                    style="height: 2rem"
                    readonly
                />
            </div>
            <div class="d-flex">
                <span class="fs-6">Discount Amount:</span
                ><input
                    type="text"
                     name="totalDiscount" id="totalDiscount"
                    class="form-control mx-2 fs-6 my-auto"
                    style="height: 2rem"
                    readonly
                />
            </div>
            <div class="d-flex">
                <span class="fs-3">Total:</span
                ><input
                    type="text"
                    name="total"
                    id="total"
                    class="form-control mx-2 fs-5 my-auto"
                    readonly
                />
            </div>
        </div>
    </form>
    <form id="addProductForBill" class="row col-12" autocomplete="off">
        <div class="col-12 row">
             <div class="col-2 d-none">
                <label for="productPId">Product ID</label>
                <input type="text" name="productPId" id="productPId" class="form-control" />
            </div>
            <div class="col-2">
                <label for="pId">Product ID</label>
                <input type="text" name="pId" id="pId" class="form-control" />
            </div>
            <div class="col-4">
                <label for="pName">Product Name</label>
                <input
                    type="text"
                    name="pName"
                    id="pName"
                    class="form-control"
                    readonly
                />
            </div>
            <div class="col-1">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" id="qty" class="form-control" />
            </div>
            <div class="col-2">
                <label for="rate">Rate</label>
                <input type="text" name="rate" id="rate" class="form-control" />
            </div>
            <div class="col-2">
                <label for="mrp">MRP</label>
                <input type="text" name="mrp" id="mrp" class="form-control" />
            </div>
            <div class="col-1 d-none">
                <label for="discount">Discount</label>
                <input
                    type="text"
                    name="discount"
                    id="discount"
                    class="form-control"
                />
            </div>
            <div class="col-1 d-none">
                <label for="inventoryId">Inventory ID</label>
                <input
                    type="text"
                    name="inventoryId"
                    id="inventoryId"
                    class="form-control"
                />
            </div>
            <div class="col-1 d-none">
                <label for="max-quantity">Max Quantity</label>
                <input
                    type="text"
                    name="max-qty"
                    id="max-qty"
                    class="form-control"
                />
            </div>
            <div class="col-1 px-1">
                <button class="btn btn-success mt-4 w-100" id="addBtn" disabled>
                    ADD
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
@section('customer-data') @include('customerInfo',compact('customers')) @endsection
@section('product-data') @include('productInfo',compact('products')) @endsection
@section('product_sub_info') @include('partials.viewProductStock') @endsection
@section('bottom-script-section')<script type="module" src="{{ asset('js/bill.js') }}"></script> @endsection
