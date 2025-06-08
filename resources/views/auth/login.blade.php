
@extends("layout.default")
@section("title", "Login")
@section("content")
<main style="background-color:rgb(133, 239, 135); min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/toplogo.png') }}" alt="Logo" style="max-width: 150px;">
                </div>

                @if(session()->has("success")) 
                    <div class="alert alert-success">
                        {{session()->get("success")}}
                    </div>
                @endif
                @if(session()->has("error")) 
                    <div class="alert alert-success">
                        {{session()->get("error")}}
                    </div>
                @endif
                <div class="card">
                    <h3 class="card-header text-center">Login</h3>
                    <div class="card-body">
                        <form method="POST" action="{{ route("login.post") }}" autocomplete="off">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="text" placeholder="Email"
                                    id="email" class="form-control" 
                                    name="email"
                                    required autofocus autocomplete="off">
                                @if ($errors->has('email'))
                                    <span class="text-danger">
                                        {{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" placeholder="Password"
                                    id="password" class="form-control"
                                    name="password" required autocomplete="off">
                                @if ($errors->has('password'))
                                    <span class="text-danger">
                                        {{ $errors->first('password') }}</span>

                                @endif
                            </div>
                            <div class="d-grid mx-auto">
                                <button type="submit"
                                    class="btn btn-dark btn-block"  style="background-color: #009900; color: white;">Signin</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
