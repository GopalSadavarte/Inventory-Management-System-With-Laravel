<div
    class="container bg-light position-fixed top-0 rounded shadow-lg user-select-none w-100 d-none"
    id="product-container"
    style="height: 97%; margin-inline: 8.3%"
>
    <div class="d-flex">
        <h2 class="heading mx-3 w-50 mt-4 text-primary">
            Product Info
        </h2>
        <div class="w-75 mx-2 mt-4">
            <input
                type="text"
                name="search"
                id="search-product"
                class="form-control"
                placeholder="Search by product name.."
            />
        </div>
        <div class="mx-2 mt-4 d-flex h-25">
            <span class="btn btn-success">Setting</span>
            <span class="btn btn-info mx-1">Help</span>
        </div>
        <div>
            <img
                src="{{ asset('icons/close.png') }}"
                id="product-close-img"
                style="height: 0.7rem; width: 0.7rem; cursor: pointer"
            />
        </div>
    </div>
    <div
        class="my-3 px-3 mx-auto overflow-scroll w-100 h-75"
        style="scrollbar-width: none"
    >
        <table class="table table-bordered table-striped w-100" id="productTable">
            <tr class="text-center">
                <th>ID</th>
                <th>Name</th>
                <th>Rate</th>
                <th>MRP</th>
                <th>Discount</th>
                <th>GST</th>
            </tr>
            @foreach ($products as $product)
            <tr class="tableRow">
                <td class="d-none">{{$product->id}}</td>
                <td>{{$product->product_id}}</td>
                <td class="d-none">{{$product->group_no}}</td>
                <td class="d-none">{{$product->sub_group_no}}</td>
                <td class="searchKey">{{$product->product_name}}</td>
                <td class="d-none">{{$product->weight}}</td>
                <td>{{$product->rate}}</td>
                <td>{{$product->MRP}}</td>
                <td>{{$product->discount}}</td>
                <td>{{$product->GST}}</td>
                <td class="d-none">{{$product->GSTOn}}</td>
            </tr>
            @endforeach
            <tr class="text-center">
                <td colspan="6">...</td>
            </tr>
        </table>
    </div>
    <div class="container my-2 text-center">
        <button class="btn btn-danger w-25" id="product-select-button" disabled>
            Select
        </button>
    </div>
</div>
