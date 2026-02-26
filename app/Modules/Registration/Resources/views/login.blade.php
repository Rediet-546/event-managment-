@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center py-4">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Header -->
                <div class="card-header bg-primary text-white text-center py-4 border-0">
                    <h3 class="mb-0">Welcome Back!</h3>
                    <p class="mb-0 opacity-75">Login to your account</p>
                </div>

                <!-- Body -->
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FIXED: Use 'login' route name (not 'login.submit') --}}
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        {{-- FIXED: Use 'email' field name to match controller --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter your email"
                                       required 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                Forgot Password?
                            </a>
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>

                        <!-- Demo Credentials -->
                        <div class="alert alert-info mt-4 mb-0">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Demo Credentials:</strong><br>
                                Admin: admin1@events.com / Password123!<br>
                                Creator: creator1@events.com / Password123!<br>
                                Attendee: attendee1@events.com / Password123!
                            </small>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
                                    Sign up here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').click(function() {
        const input = $('#password');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Loading state on submit
    $('#loginForm').submit(function() {
        $('#loginBtn').prop('disabled', true);
        $('#loginBtn').html('<span class="spinner-border spinner-border-sm me-2"></span>Logging in...');
    });
});
</script>
@endpush