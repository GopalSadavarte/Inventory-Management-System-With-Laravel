@extends('template') @push('title') Item Groups @endpush
@section('main-section')
<div class="container d-flex">
    <div
        class="container mt-3 p-4 bg-light rounded-3 mx-3"
        style="margin: 0 auto 0 0"
    >
        <div
            class="alert @session('group_success') alert-success @endsession @session('group_update') alert-primary @endsession @session('group_delete') alert-danger @endsession"
        >
            <span>
                @session('group_success') {!!session('group_success')!!}
                @endsession @session('group_update')
                {!!session('group_update')!!} @endsession
                @session('group_delete') {!!session('group_delete')!!}
                @endsession
            </span>
        </div>
        <div class="heading mb-3">
            <h2 class="text-left text-dark">Group</h2>
        </div>
        <div class="form-box g-3 col-12">
            <form
                autocomplete="off"
                id="addGroupForm"
                method="post"
                action="{{ route('group.store') }}"
            >
                @csrf
                <span id="method"></span>
                <div class="form-element col-10">
                    <label for="groupId" class="form-label"> Group ID </label>
                    <input
                        type="text"
                        name="groupId"
                        id="groupId"
                        value="{{ $lastGroupNo }}"
                        class="form-control"
                        readonly
                    />
                    <small class="text-danger error my-1">
                        @error('groupId')
                        {{ $message }}
                        @enderror
                    </small>
                </div>
                <div class="form-element col-10 mt-2">
                    <label for="group_name" class="form-label">
                        Group Name
                    </label>
                    <input
                        type="text"
                        name="group"
                        id="group_name"
                        value="{{old('group')}}"
                        placeholder="Enter Group Name"
                        class="form-control"
                    />
                    <small class="text-danger error my-1">
                        @error('group')
                        {{ $message }}
                        @enderror
                    </small>
                </div>
                <div class="form-element col-10 mt-3 g-3">
                    <button
                        class="btn btn-warning col-2 mx-2 group-btn"
                        id="newEntry"
                    >
                        New
                    </button>
                    <button
                        class="btn btn-info text-dark col-2 mx-1 group-btn"
                        id="delete-btn"
                    >
                        Delete
                    </button>
                    <button
                        class="btn btn-success text-light col-2 group-btn"
                        id="edit-btn"
                    >
                        Edit
                    </button>
                    <input
                        type="submit"
                        value="Save"
                        class="btn btn-primary col-2 mx-1 group-btn"
                        id="submit-btn"
                        itemid="submit"
                        disabled
                    />
                    <input
                        type="reset"
                        value="Cancel"
                        class="btn btn-danger col-2 mx-2 group-btn"
                        id="cancel-btn"
                    />
                </div>
            </form>
        </div>
    </div>
    <div
        class="container mt-3 p-4 bg-light rounded-3"
        style="margin: 0 auto 0 0"
    >
        <div
            class="alert @session('sub_group_success') alert-success @endsession @session('sub_group_update') alert-primary @endsession @session('sub_group_delete') alert-danger @endsession"
        >
            <span>
                @session('sub_group_success') {!!session('sub_group_success')!!}
                @endsession @session('sub_group_update')
                {!!session('sub_group_update')!!} @endsession
                @session('sub_group_delete') {!!session('sub_group_delete')!!}
                @endsession
            </span>
        </div>
        <div class="heading mb-3">
            <h2 class="text-left text-dark">Sub Group</h2>
        </div>
        <div class="form-box g-3 col-12">
            <form
                autocomplete="off"
                id="addSubGroupForm"
                method="post"
                action="{{ route('storeSubGroup') }}"
            >
                @csrf
                <span id="method1"></span>
                <div class="form-element col-10">
                    <label for="subGroupId" class="form-label">
                        Sub Group ID
                    </label>
                    <input
                        type="text"
                        name="subGroupId"
                        value="{{ $lastSubGroupNo }}"
                        id="subGroupId"
                        class="form-control"
                        readonly
                    />
                    <small class="text-danger error my-1">
                        @error('subGroupId')
                            {{$message}}
                        @enderror
                    </small>
                </div>
                <div class="form-element col-10 mt-2">
                    <label for="sub_group_name" class="form-label">
                        Sub Group</label
                    >
                    <input
                        type="text"
                        name="sub_group_name"
                        id="sub_group_name"
                        value="{{old('sub_group_name')}}"
                        placeholder="Enter Sub Group Name"
                        class="form-control"
                    />
                    <small class="text-danger">
                        @error('sub_group_name')
                            {{$message}}
                        @enderror
                    </small>
                </div>
                <div class="form-element col-10 mt-2">
                    <label for="group_name_sub" class="form-label">
                        Select Group</label
                    >
                    <select
                        name="group_name_sub"
                        id="group_name_sub"
                        class="form-control"
                    >
                        <option disabled selected>Select Group</option>
                        @foreach ($groups as $group)
                        <option class="group_option" value="{{$group->group_id}}">
                            {{$group->group_name}}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-danger">
                        @error('group_name_sub')
                            {{$message}}
                        @enderror
                    </small>
                </div>
                <div class="form-element col-10 mt-3 g-3">
                    <button class="btn btn-warning col-2 mx-2 subGroupBtn" id="newEntry1">
                        New
                    </button>
                    <button class="btn btn-info col-2 mx-1 subGroupBtn" id="delete-btn1">
                        Delete
                    </button>
                    <button
                        class="btn btn-success text-light col-2 subGroupBtn"
                        id="edit-btn1"
                    >
                        Edit
                    </button>
                    <input
                        type="submit"
                        value="Save"
                        class="btn btn-primary col-2 mx-1 subGroupBtn"
                        id="submit-btn1"
                        itemid="submit"
                        disabled
                    />
                    <input
                        type="reset"
                        value="Cancel"
                        class="btn btn-danger col-2 mx-2 subGroupBtn"
                        id="cancel-btn1"
                    />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection @section('bottom-script-section')
<script type="module" src="{{ asset('js/group.js') }}"></script>
@endsection
