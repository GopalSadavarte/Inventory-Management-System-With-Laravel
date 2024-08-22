@extends('template')
@push('title')
    Item Info
@endpush
@section('main-section')
    <div class="container mt-3 shadow-lg p-5 bg-light rounded-3">
            <div class="alert
                {{-- @if (!isset($_SESSION['success']) && !isset($_SESSION['update']) && !isset($_SESSION['delete']))d-none @endif
                @isset($_SESSION['success'])alert-success @endisset
                @isset($_SESSION['update'])alert-primary @endisset
                @isset($_SESSION['delete'])alert-danger @endisset --}}
                @if (!session('update')&&!session('delete')&&!session('success'))d-none @endif
                @session('update')alert-primary @endsession
                @session('delete')alert-danger @endsession
                @session('success')alert-success @endsession
            ">
            <span>
                {{-- @isset($_SESSION['success']){!!$_SESSION['success']!!} @php unset($_SESSION['success'])@endphp @endisset
                @isset($_SESSION['update']){!!$_SESSION['update']!!}@php unset($_SESSION['update'])@endphp @endisset
                @isset($_SESSION['delete']){!!$_SESSION['delete']!!}@php unset($_SESSION['delete'])@endphp @endisset --}}
                @session('update'){!!session('update')!!}@endsession
                @session('delete'){!!session('delete')!!}@endsession
                @session('success'){!!session('success')!!}@endsession
            </span>
            </div>
            <div class="heading mb-3">
                <h2 class="text-left text-dark">New Product</h2>
            </div>
            <div class="form-box row g-3 col-12">
                <form id="productForm" method="post" autocomplete="off" action="{{route('product.store')}}">
                    @csrf
                    <span id="method"></span>
                    <div class="col-12 d-flex g-3 mt-1">
                        <div class="form-element col-3">
                            <label for="stdId" class="form-label">
                                Product ID:
                            </label>
                            <input
                                type="text"
                                name="p_id"
                                id="p_id"
                                value="{{$productId}}"
                                class="form-control formInputField"
                                placeholder="Product ID"
                                readonly
                            />
                            <small class="text-danger m-0 p-0" id="error-id"></small>
                        </div>
                        <div class="form-element col-9 mx-1">
                            <label for="name" class="form-label">Product Name</label>
                            <input
                                type="text"
                                name="p_name"
                                id="name"
                                value="{{old('p_name')}}"
                                class="form-control formInputField"
                                placeholder="Enter Product Name"
                                readonly
                            />
                            <small class="text-danger m-0 p-0 error">
                                @error('p_name')
                                    {{$message}}
                                @enderror
                            </small>
                        </div>
                    </div>
                    <div class="col-12 d-flex g-3 mt-1">
                        <div class="form-element col-6">
                            <label for="group" class="form-label">
                                Group
                            </label>
                           <select name="group" id="group" class="form-control formInputField" disabled>
                                <option selected disabled>Select Item Group</option>
                                @foreach ($groups as $group)
                                    <option value="{{$group->group_id}}">{{$group->group_name}}</option>
                                @endforeach
                           </select>
                            <small class="text-danger m-0 p-0 error">
                                @error('group')
                                    {{$message}}
                                @enderror
                            </small>
                        </div>
                        <div class="form-element col-6 mx-1">
                            <label for="sub_group" class="form-label">
                                Sub-Group
                            </label>
                           <select name="sub_group" id="sub_group" class="form-control formInputField" disabled>
                                <option selected disabled>Select Item Sub-Group</option>
                                @foreach ($sub_groups as $group)
                                    <option value="{{$group->sub_group_id}}">{{$group->sub_group_name}}</option>
                                @endforeach
                           </select>
                            <small class="text-danger m-0 p-0 error">
                                @error('sub_group')
                                    {{$message}}
                                @enderror
                            </small>
                        </div>
                    </div>
                    <div class="form-element mt-1 col-12 d-none">
                        <label for="qty" class="form-label">Quantity</label>
                        <input
                            type="number"
                            name="qty"
                            id="qty"
                            value="1"
                            class="form-control formInputField"
                            placeholder="Default Quantity"
                            readonly
                        />
                        <small class="text-danger p-0 m-0 error">
                            @error('qty')
                                {{$message}}
                            @enderror
                        </small>
                    </div>
                    <div class="col-12 d-flex g-3 mt-1">
                        <div class="form-element col-4">
                            <label for="weight" class="form-label">
                                weight:
                            </label>
                            <input
                                type="text"
                                name="weight"
                                id="weight"
                                value="{{old('weight')}}"
                                class="form-control formInputField"
                                placeholder="Product weight"
                                readonly
                            />
                               <small class="text-danger m-0 p-0 error">
                                @error('weight')
                                    {{$message}}
                                @enderror
                        </small>
                        </div>
                        <div class="form-element col-4 mx-1">
                            <label for="rate" class="form-label">Rate</label>
                            <input
                                type="text"
                                name="p_rate"
                                id="rate"
                                value="{{old('p_rate')}}"
                                class="form-control formInputField"
                                placeholder="rate"
                                readonly
                            />
                            <small class="text-danger m-0 p-0 error">
                                @error('p_rate')
                                    {{$message}}
                                @enderror
                            </small>
                        </div>
                        <div class="form-element col-4">
                            <label for="mrp" class="form-label">
                                MRP
                            </label>
                            <input
                                type="number"
                                name="p_mrp"
                                id="mrp"
                                value="{{old('p_mrp')}}"
                                class="form-control formInputField"
                                placeholder="Product MRP"
                                readonly
                            />
                              <small class="text-danger m-0 p-0 error">
                                @error('p_mrp')
                                    {{$message}}
                                @enderror
                            </small>
                        </div>
                    </div>
                    <div class="col-12 d-flex g-3 mt-1">
                        <div class="form-element col-4">
                            <label for="discount" class="form-label">Discount</label>
                            <input
                                type="text"
                                name="discount"
                                id="discount"
                                value="{{old('discount')}}"
                                class="form-control formInputField"
                                placeholder="Discount in %"
                                readonly
                            />
                            <small class="text-danger m-0 p-0 error">
                                @error('discount')
                                    {{$message}}
                                @enderror
                            </small>
                        </div>
                        <div class="form-element col-4 mx-1">
                            <label for="gst" class="form-label">GST</label>
                            <select name="gst" id="gst" class="form-control formInputField" disabled>
                                <option value="12" selected>12%</option>
                                <option value="18">18%</option>
                                <option value="25">25%</option>
                                <option value="32">32%</option>
                            </select>
                        </div>
                         <div class="form-element col-4">
                            <label for="gstOn" class="form-label">GST On</label>
                            <select name="gstOn" id="gstOn" class="form-control formInputField" disabled>
                                <option value="rate" selected>Sale Rate</option>
                                <option value="MRP">MRP</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-element mt-4 col-12">
                        <input
                            type="submit"
                            value="Submit"
                            class="btn btn-primary col-2 mx-3 productBtn"
                            id="submit-btn"
                            itemid="submit"
                            disabled
                        />
                        <input
                            type="reset"
                            value="Cancel"
                            class="btn btn-danger col-2 mx-3 productBtn"
                            id="cancel-btn"
                        />
                        <button class="btn btn-success col-2 mx-3 productBtn" id="newEntry">
                            New
                        </button>
                        <button class="btn btn-warning text-light col-2 mx-3 productBtn" id="delete-btn">
                            Delete
                        </button>
                        <button class="btn btn-info text-light col-2 mx-2 productBtn" id="edit-btn">
                            Edit
                        </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('bottom-script-section')
    <script src="{{ asset('js/script.js') }}"></script>
@endsection

@section('product-data')
    @include('productInfo',compact('products'))
@endsection
