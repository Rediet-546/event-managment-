@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4 text-center p-5">
                <div class="mb-4">
                    <div class="icon-circle bg-primary bg-opacity-10 mx-auto mb-4">
                        <i class="fas fa-envelope fa-4x text-primary"></i>
                    </div>
                    <h2 class="mb-3">Verify Your Email</h2>
                    <p class="text-muted">
                        We've sent a verification link to <strong>{{ auth()->user()->email }}</strong>
                    </p>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Please click the link in the email to verify your account.
                </div>

                <div class="mt-4">
                    <p class="text-muted mb-3">Didn't receive the email?</p>
                    <form action="{{ route('verification.resend') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>Resend Verification Email
                        </button>
                    </form>
                </div>

                <div class="mt-4">
                    <a href="{{ route('logout') }}" 
                       class="text-decoration-none"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.icon-circle {
    width: 100px;
    height: 100px;
    line-height: 100px;
    border-radius: 50%;
    margin: 0 auto;
}
</style>
@endpush