@extends('layouts.guest')

@section('title', 'Account Pending Approval')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Header -->
                <div class="card-header bg-warning text-white text-center py-4">
                    <div class="mb-3">
                        <div class="icon-circle bg-white bg-opacity-25 mx-auto">
                            <i class="fas fa-clock fa-4x text-white"></i>
                        </div>
                    </div>
                    <h2 class="mb-0">Account Pending Approval</h2>
                </div>

                <!-- Body -->
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <h4 class="mb-3">Thank you for registering, {{ auth()->user()->first_name }}!</h4>
                        <p class="text-muted">
                            Your event creator account is currently under review by our administrators.
                            This process typically takes 1-2 business days.
                        </p>
                    </div>

                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <div class="text-start">
                                <strong>What happens next?</strong>
                                <p class="mb-0 small">
                                    You'll receive an email notification at <strong>{{ auth()->user()->email }}</strong> 
                                    once your account is approved. You'll then be able to create and manage events.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="progress-steps mb-4">
                        <div class="row">
                            <div class="col-4">
                                <div class="step completed">
                                    <div class="step-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="step-label">Registered</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="step active">
                                    <div class="step-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <span class="step-label">Under Review</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="step">
                                    <div class="step-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <span class="step-label">Approved</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <i class="fas fa-building text-success mb-2"></i>
                                <h6 class="mb-1">{{ auth()->user()->organization_name ?? 'Your Organization' }}</h6>
                                <small class="text-muted">Organization</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <i class="fas fa-phone text-info mb-2"></i>
                                <h6 class="mb-1">{{ auth()->user()->phone ?? 'Phone' }}</h6>
                                <small class="text-muted">Contact</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Return to Homepage
                        </a>
                        <a href="#" class="btn btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="fas fa-headset me-2"></i>Contact Support
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        Need help? Email us at <a href="mailto:support@events.com">support@events.com</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contact Support</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
.step {
    text-align: center;
    position: relative;
}
.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    color: #6c757d;
    border: 2px solid transparent;
}
.step.completed .step-icon {
    background: #28a745;
    color: white;
}
.step.active .step-icon {
    border-color: #ffc107;
    color: #ffc107;
    background: white;
}
.step-label {
    font-size: 12px;
    color: #6c757d;
}
.step.completed .step-label {
    color: #28a745;
}
.step.active .step-label {
    color: #ffc107;
    font-weight: bold;
}
</style>
@endpush