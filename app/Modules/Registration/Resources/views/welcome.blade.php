@extends('layouts.guest')

@section('title', 'Welcome to EventHub')

@section('content')
<div class="container-fluid p-0">
    <!-- Hero Section -->
    <section class="hero-section bg-gradient-primary text-white py-5">
        <div class="container py-5">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInUp">
                        Discover Amazing <span class="text-warning">Events</span> Near You
                    </h1>
                    <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        Join thousands of event enthusiasts. Book tickets, meet new people, 
                        and create unforgettable memories with EventHub.
                    </p>
                    
                    @guest
                        <div class="d-flex gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-user-plus me-2"></i>Get Started
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>
                    @else
                        <a href="{{ route('events.index') }}" class="btn btn-light btn-lg px-4 animate__animated animate__fadeInUp animate__delay-2s">
                            <i class="fas fa-calendar-alt me-2"></i>Browse Events
                        </a>
                    @endguest
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/hero-illustration.svg') }}" 
                         alt="Events" 
                         class="img-fluid animate__animated animate__fadeInRight">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Why Choose EventHub?</h2>
                <p class="lead text-muted">The complete platform for event discovery and management</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center p-4 feature-card">
                        <div class="feature-icon-wrapper mb-4">
                            <div class="feature-icon bg-primary bg-opacity-10">
                                <i class="fas fa-calendar-check fa-3x text-primary"></i>
                            </div>
                        </div>
                        <h4>Easy Booking</h4>
                        <p class="text-muted">Book your favorite events in just a few clicks with our seamless booking system.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center p-4 feature-card">
                        <div class="feature-icon-wrapper mb-4">
                            <div class="feature-icon bg-success bg-opacity-10">
                                <i class="fas fa-shield-alt fa-3x text-success"></i>
                            </div>
                        </div>
                        <h4>Secure Payments</h4>
                        <p class="text-muted">Your payments are secure with our encrypted payment gateway integration.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center p-4 feature-card">
                        <div class="feature-icon-wrapper mb-4">
                            <div class="feature-icon bg-info bg-opacity-10">
                                <i class="fas fa-chart-line fa-3x text-info"></i>
                            </div>
                        </div>
                        <h4>For Creators</h4>
                        <p class="text-muted">Powerful tools to create, manage and analyze your events.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works-section bg-light py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">How It Works</h2>
                <p class="lead text-muted">Get started in three simple steps</p>
            </div>

            <div class="row">
                <div class="col-md-4 text-center step">
                    <div class="step-number mx-auto mb-3">1</div>
                    <h4>Create Account</h4>
                    <p class="text-muted">Sign up as an attendee or event creator</p>
                </div>
                <div class="col-md-4 text-center step">
                    <div class="step-number mx-auto mb-3">2</div>
                    <h4>Find or Create Events</h4>
                    <p class="text-muted">Browse events or create your own</p>
                </div>
                <div class="col-md-4 text-center step">
                    <div class="step-number mx-auto mb-3">3</div>
                    <h4>Book & Enjoy</h4>
                    <p class="text-muted">Book tickets and enjoy amazing experiences</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-3 text-center mb-3">
                    <div class="stat-card">
                        <h3 class="display-4 fw-bold text-primary">10K+</h3>
                        <p class="text-muted">Active Users</p>
                    </div>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="stat-card">
                        <h3 class="display-4 fw-bold text-primary">500+</h3>
                        <p class="text-muted">Events Hosted</p>
                    </div>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="stat-card">
                        <h3 class="display-4 fw-bold text-primary">50+</h3>
                        <p class="text-muted">Cities</p>
                    </div>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="stat-card">
                        <h3 class="display-4 fw-bold text-primary">24/7</h3>
                        <p class="text-muted">Support</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section bg-gradient-primary text-white py-5">
        <div class="container py-5 text-center">
            <h2 class="display-6 fw-bold mb-4">Ready to start your event journey?</h2>
            @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
                    Create Free Account
                </a>
            @else
                <a href="{{ route('events.index') }}" class="btn btn-light btn-lg px-5">
                    Browse Events
                </a>
            @endguest
        </div>
    </section>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.min-vh-75 {
    min-height: 75vh;
}
.feature-icon-wrapper {
    width: 100px;
    height: 100px;
    margin: 0 auto;
}
.feature-icon {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
.feature-card {
    transition: transform 0.3s;
}
.feature-card:hover {
    transform: translateY(-10px);
}
.step-number {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
}
.stat-card {
    padding: 20px;
    border-radius: 10px;
    background: white;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
</style>
@endpush