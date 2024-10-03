@extends('template') @section('main-section')
<div class="container my-5 login-container p-3">
    @session('invalid')
        <div class="alert alert-danger">
            {!!session('invalid')!!}
            <button class="btn-close" id="close-btn"></button>
        </div>
    @endsession
    <div class="login-page w-50 mx-auto shadow p-3 rounded-lg">
        <div class="heading text-center text-dark my-2">
            <h2 class="heading font-open-sans my-3">Login</h2>
        </div>
        <div class="form-container">
            <form
                action="{{ route('authorize') }}"
                method="POST"
            >
            @csrf
                <div class="form-element">
                    <label for="username" class="form-label">
                        User Name:
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="email"
                        value="{{old('email')}}"
                        placeholder="Enter your email"
                        class="form-control"
                    />
                    <small class="my-2 text-danger">@error('email')
                        {{$message}}
                    @enderror</small>
                </div>
                <div class="form-element">
                    <label for="password" class="form-label">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        value="{{old('password')}}"
                        name="password"
                        placeholder="Enter your password"
                        class="form-control"
                    />
                    <small class="my-2 text-danger">@error('password')
                        {{$message}}
                    @enderror</small>
                </div>
                <div class="form-btn my-3">
                   <input type="submit" value="Login" class="btn w-100 btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('bottom-script-section')
    <script src="{{asset('js/Auth/login.js')}}"></script>
@endsection
