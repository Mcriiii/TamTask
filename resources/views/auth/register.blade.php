@extends("layout.default")
@section("title", "Register")
@section("content")
<main class="mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                @if(session()->has("success")) 
                    <div class="alert alert-success">
                        {{ session()->get("success") }}
                    </div>
                @endif
                @if(session()->has("error")) 
                    <div class="alert alert-danger">
                        {{ session()->get("error") }}
                    </div>
                @endif
                <div class="card">
                    <h3 class="card-header text-center">Register</h3>
                    <div class="card-body">
                        <form method="POST" action="{{ route('register.post') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="text" placeholder="First Name"
                                    id="first_name" class="form-control" 
                                    name="first_name" value="{{ old('first_name') }}" autofocus>
                                @if ($errors->has('first_name'))
                                    <span class="text-danger">
                                        {{ $errors->first('first_name') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" placeholder="Last Name"
                                    id="last_name" class="form-control" 
                                    name="last_name" value="{{ old('last_name') }}">
                                @if ($errors->has('last_name'))
                                    <span class="text-danger">
                                        {{ $errors->first('last_name') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="email" placeholder="Email"
                                    id="email" class="form-control" name="email"
                                    value="{{ old('email') }}" required>
                                @if ($errors->has('email'))
                                    <span class="text-danger">
                                        {{ $errors->first('email') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" placeholder="Password"
                                    id="password" class="form-control"
                                    name="password" required>
                                @if ($errors->has('password'))
                                    <span class="text-danger">
                                        {{ $errors->first('password') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" placeholder="Confirm Password"
                                    id="password_confirmation" class="form-control"
                                    name="password_confirmation" required>
                                @if ($errors->has('password_confirmation'))
                                    <span class="text-danger">
                                        {{ $errors->first('password_confirmation') }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-grid mx-auto">
                                <button type="submit"
                                    class="btn btn-dark btn-block">Sign up</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
