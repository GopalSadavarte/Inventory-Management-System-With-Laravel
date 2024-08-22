@extends('template') @push('title') Stock Entry @endpush
@section('main-section')
<div class="bg-light px-4 my-2 py-2 shadow rounded mx-4 h-100">
    <div class="mt-2 d-flex">
        <h3 class="heading">Stock Entry</h3>
        <span class="mx-3 my-2">
            <strong class="text-danger">Note:</strong>
            You must provide atleast dealer name or all info for accurate report!
        </span>
        @session('exception')
            <x-alert id="alert-box" message="{{session('exception')}}"/>
        @endsession
    </div>
    <form id="stockEntryForm" method="post" action="{{ route('stock.store') }}" autocomplete="off">
        @csrf
        <span id="method"></span>
        <div class="content">
            <div class="d-flex">
                <div class="row g-5 col-12">
                    <div class="d-flex col-12">
                        <div class="d-none">
                            <label for="dealerId">Dealer ID</label>
                            <input
                                type="text"
                                name="dealerId"
                                id="dealerId"
                                class="form-control dealer"
                                placeholder="Dealer ID"
                            />
                        </div>
                        <div class="col-4 mx-1">
                            <label for="dealerName">Name</label>
                            <input
                                type="text"
                                name="dealerName"
                                id="dealerName"
                                class="form-control dealer"
                                placeholder="Dealer Name"
                            />
                        </div>
                        <div class="col-2">
                            <label for="contactNumber">Contact</label>
                            <input
                                type="text"
                                name="contactNumber"
                                id="contactNumber"
                                class="form-control dealer"
                                placeholder="Contact No."
                            />
                        </div>
                        <div class="col-3 mx-1">
                            <label for="Email">Email</label>
                            <input
                                type="text"
                                name="Email"
                                id="Email"
                                class="form-control dealer"
                                placeholder="Email Address"
                            />
                        </div>
                        <div class="col-3">
                            <label for="GSTNumber">GST No.</label>
                            <input
                                type="text"
                                name="GSTNumber"
                                id="GSTNumber"
                                class="form-control dealer"
                                placeholder="GSTIN"
                            />
                        </div>
                    </div>
                    <div class="d-flex my-auto col-12">
                          <div class="col-2 mx-2">
                            <label for="counterNumber">Counter No.</label>
                            <input
                                type="number"
                                name="counterNumber"
                                id="counterNumber"
                                value="1"
                                readonly
                                class="form-control"
                            />
                        </div>
                        <div class="col-2 mx-2">
                            <label for="entryNumber">Entry No.</label>
                            <input
                                type="number"
                                name="entryNumber"
                                id="entryNumber"
                                value="{{ $lastEntry }}"
                                readonly
                                class="form-control"
                            />
                            <small
                                id="entryNoError"
                                class="text-danger"
                            ></small>
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
                            <select
                                name="print"
                                id="print"
                                class="form-control"
                            >
                                <option value="YES">Yes</option>
                                <option value="NO" selected>No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section overflow-scroll w-100 shadow-sm px-1 py-2">
            <table class="table table-bordered table-striped" id="stock-table">
                <tr>
                    <th class="d-none">id</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Pur.rate</th>
                    <th>Sale rate</th>
                    <th>MRP</th>
                    <th>GST</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>With GST</th>
                    <th>Amt.</th>
                </tr>
            </table>
        </div>
        <div class="mt-2 col-12 row mx-auto">
            <button class="btn btn-warning my-3 col-1 button" disabled>
                New
            </button>
            <button class="btn btn-info col-1 my-3 mx-1 button" disabled>
                Delete
            </button>
            <button class="btn btn-success col-1 my-3  button" disabled>
                Edit
            </button>
             <input
                type="submit"
                value="Save"
                class="btn btn-primary my-3 mx-1 col-1 button"
                id="submit"
            />
            <input
                type="reset"
                name="reset"
                value="Cancel"
                id="reset"
                class="btn btn-danger col-1 my-3  button"
            />
                <div class="col-2">
                <label for="totalStock">Total Stock</label>
                <input
                    type="text"
                    name="totalStock"
                    id="totalStock"
                    class="form-control"
                    readonly
                />
            </div>
            <div class="col-2" style="margin-inline: -1rem">
                <label for="totalTax">Total Tax (Rs.)</label>
                <input
                    type="text"
                    name="totalTax"
                    id="totalTax"
                    class="form-control"
                    readonly
                />
            </div>
            <div class="col-2 mx-0">
                <label for="stockAmt">Stock Amt.(With Tax)</label>
                <input
                    type="text"
                    name="stockAmt"
                    id="stockAmt"
                    class="form-control"
                    readonly
                />
            </div>
            <div class="col-1" style="margin-left: -1rem">
                <label for="stockEntries">Entries</label>
                <input
                    type="text"
                    name="stockEntries"
                    id="stockEntries"
                    class="form-control"
                    readonly
                />
            </div>
        </div>
    </form>
</div>
@endsection @section('product-data') @include('productInfo',compact('products'))
@endsection
@section('customer-data')
    @include('dealerInfo',compact('dealers'))
@endsection
@section('bottom-script-section')
<script type="module" src="{{ asset('js/stock.js') }}"></script>
@endsection
