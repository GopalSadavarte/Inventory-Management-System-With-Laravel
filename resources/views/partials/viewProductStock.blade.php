<div
    class="container w-50 h-75 p-4 m-0 bg-light shadow-sm rounded d-none"
    id="viewByStock"
>
    <div class="table-info">
        <div class="heading head-div">
            <h1 class="heading mb-1 text-center fs-4 text-primary">
                Select Product
            </h1>
            <img src="{{ asset('icons/close.png') }}" id="close-sub-stock" />
        </div>
        <div class="table-data overflow-scroll">
            <table
                class="table table-bordered table-striped text-center"
                id="availableStock"
            >
                <tr class="table-heading">
                    <th>ID</th>
                    <th>Rate</th>
                    <th>MRP</th>
                    <th>Qty.</th>
                    <th>MFD</th>
                    <th>EXP</th>
                </tr>
            </table>
        </div>
        <div class="selection-btn text-center p-2">
            <button class="btn btn-danger" id="product-sub-info-btn" disabled>
                select
            </button>
        </div>
    </div>
</div>
