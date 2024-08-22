<div
    class="container bg-light position-fixed top-0 rounded shadow-lg user-select-none w-100 d-none"
    id="customer-container"
    style="height: 97%; margin-inline: 8.3%"
>
    <div class="d-flex">
        <h2 class="heading mx-3 w-50 mt-4 text-primary">
            Customer Info
        </h2>
        <div class="w-75 mx-2 mt-4">
            <input
                type="search"
                name="search"
                id="search"
                class="form-control"
                placeholder="Search by customer name.."
            />
        </div>
        <div class="mx-2 mt-4 d-flex h-25">
            <span class="btn btn-success">Setting</span>
            <span class="btn btn-info mx-1">Help</span>
        </div>
        <div>
            <img
                src="{{ asset('icons/close.png') }}"
                id="customer-close-img"
                style="height: 0.7rem; width: 0.7rem; cursor: pointer"
            />
        </div>
    </div>
    <div
        class="my-3 px-3 mx-auto overflow-scroll w-100 h-75"
        style="scrollbar-width: none"
    >
        <table class="table table-bordered table-striped w-100" id="customerTable">
            <tr class="text-center">
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Pending Amount</th>
            </tr>
            @foreach ($customers as $customer)
            <tr class="tableRow">

                <td>{{$customer->id}}</td>
                <td class="searchKey">{{$customer->customer_name}}</td>
                <td>{{$customer->customer_email}}</td>
                <td>{{$customer->contact}}</td>
                <td>{{$customer->customer_address}}</td>
                <td>{{$customer->pending_amt}}</td>
            </tr>
            @endforeach
            <tr class="text-center">
                <td colspan="6">...</td>
            </tr>
        </table>
    </div>
    <div class="container my-2 text-center">
        <button class="btn btn-danger w-25" id="customer-select-button" disabled>
            Select
        </button>
    </div>
</div>
