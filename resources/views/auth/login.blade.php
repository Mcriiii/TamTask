@extends("layout.default")
@section("title", "Login")
@section("content")
<main style="min-height: 100vh; background: linear-gradient(to right, #a8f0aa, #48c78e);">
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="row w-100 shadow-lg rounded" style="background-color: white; overflow: hidden;">

            <!-- Left Side: Login Form -->
            <div class="col-md-6 p-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/toplogo.png') }}" alt="Logo" style="max-width: 100px;">
                    <h2 class="mt-3" style="color: #333;">Sign in to Account</h2>
                </div>

                @if(session()->has("success"))
                <div class="alert alert-success">{{ session()->get("success") }}</div>
                @endif
                @if(session()->has("error"))
                <div class="alert alert-danger">{{ session()->get("error") }}</div>
                @endif

                <form method="POST" action="{{ route("login.post") }}" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" placeholder="example@mail.com" id="email" class="form-control" name="email" required>
                        @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" placeholder="••••••••" id="password" class="form-control" name="password" required>
                        @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success rounded-pill py-2">Sign In</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <small>Privacy Policy • Terms & Conditions</small>
                </div>
            </div>

            
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center position-relative text-white" style="background: #48c78e; overflow: hidden;">
                <h1 class="mb-3 fw-bold" style="z-index: 2;">Welcome Back!</h1>
                <p style="max-width: 300px; text-align: center; z-index: 2;">We're glad you're here. Please log in to continue.</p>
                <div class="triangle" style="border-width: 60px 35px 0 35px; top: 20px; left: 40px;"></div>
                <div class="triangle" style="border-width: 40px 25px 0 25px; top: 100px; right: 60px;"></div>
                <div class="triangle" style="border-width: 50px 30px 0 30px; bottom: 80px; left: 90px;"></div>   
                <div class="triangle" style="border-width: 30px 20px 0 20px; bottom: 20px; right: 100px;"></div>
                <div class="triangle" style="border-width: 70px 40px 0 40px; top: 180px; left: 120px;"></div>
            </div>
        </div>
    </div>
</main>

<style>
    .triangle {
        width: 0;
        height: 0;
        position: absolute;
        border-style: solid;
        border-color: white transparent transparent transparent;
        opacity: 0.5;
        animation: floatTriangle 6s ease-in-out infinite alternate;
    }

    @keyframes floatTriangle {
        from {
            transform: translateY(0);
        }

        to {
            transform: translateY(-10px);
        }
    }
</style>

@endsection