<div
    class="container bg-light position-fixed top-0 rounded shadow-lg user-select-none w-100 d-none"
    id="dealer-container"
    style="height: 97%; margin-inline: 8.3%"
>
    <div class="d-flex">
        <h2 class="heading mx-3 w-50 mt-4 text-primary">
            Dealers Info
        </h2>
        <div class="w-75 mx-2 mt-4">
            <input
                type="text"
                name="search"
                id="search-dealer"
                class="form-control"
                placeholder="Search by Dealer name.."
            />
        </div>
        <div class="mx-2 mt-4 d-flex h-25">
            <span class="btn btn-success">Setting</span>
            <span class="btn btn-info mx-1">Help</span>
        </div>
        <div>
            <img
                src="{{ asset('icons/close.png') }}"
                id="dealer-close-img"
                style="height: 0.7rem; width: 0.7rem; cursor: pointer"
            />
        </div>
    </div>
    <div
        class="my-3 px-3 mx-auto overflow-scroll w-100 h-75"
        style="scrollbar-width: none"
    >
        <table class="table table-bordered table-striped w-100" id="dealerTable">
            <tr class="text-center">
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>GST No.</th>
            </tr>
            @foreach ($dealers as $dealer)
                 <tr class="tableRow">
                <td>{{$dealer->id}}</td>
                <td class="searchKey">{{$dealer->dealer_name}}</td>
                <td>{{$dealer->contact}}</td>
                <td>{{$dealer->email}}</td>
                <td>{{$dealer->GST_no}}</td>
            </tr>
            @endforeach
            <tr class="text-center tableRow">
                <td colspan="5">...</td>
            </tr>
        </table>
    </div>
    <div class="container my-2 text-center">
        <button class="btn btn-danger w-25" id="dealer-select-button" disabled>
            Select
        </button>
    </div>
</div>
