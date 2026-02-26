@extends('layouts.guest')

@section('title', 'Create Account')

@section('content')
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center py-4">
            <div class="col-md-10 col-lg-8">
                <!-- Progress Steps -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Join EventHub</h2>
                    <p class="text-muted">Choose your account type to get started</p>
                </div>

                <!-- User Type Selection -->
                <div class="row mb-4" id="userTypeSelection">
                    <div class="col-md-6 mb-3">
                        <div class="card user-type-card attendee-card h-100" data-type="attendee">
                            <div class="card-body text-center p-4">
                                <div class="type-icon mb-3">
                                    <div class="icon-circle bg-primary bg-opacity-10">
                                        <i class="fas fa-user-friends fa-3x text-primary"></i>
                                    </div>
                                </div>
                                <h4>Event Attendee</h4>
                                <p class="text-muted">I want to discover and book amazing events</p>
                                <ul class="list-unstyled text-start mt-3">
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Browse all events</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Book tickets instantly</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Track booking history</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Get recommendations</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card user-type-card creator-card h-100" data-type="creator">
                            <div class="card-body text-center p-4">
                                <div class="type-icon mb-3">
                                    <div class="icon-circle bg-success bg-opacity-10">
                                        <i class="fas fa-calendar-plus fa-3x text-success"></i>
                                    </div>
                                </div>
                                <h4>Event Creator</h4>
                                <p class="text-muted">I want to create and manage my own events</p>
                                <ul class="list-unstyled text-start mt-3">
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Create unlimited events</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Manage attendees</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Track revenue analytics</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Sell tickets online</li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i> Requires admin approval
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Forms -->
                <div class="card shadow-lg border-0 rounded-4" id="registrationForms" style="display: none;">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <button type="button" class="btn btn-link text-decoration-none mb-3" id="backToType">
                            <i class="fas fa-arrow-left me-2"></i>Change account type
                        </button>
                        <h3 class="text-center" id="formTitle">Create Attendee Account</h3>
                    </div>

                    <div class="card-body p-4">
                        <!-- Attendee Registration Form -->
                        <form method="POST" action="{{ route('register') }}" id="attendeeForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="user_type" value="attendee">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="attendee_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        id="attendee_first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="attendee_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        id="attendee_last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="attendee_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="attendee_email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="attendee_age" class="form-label">Age</label>
                                <input type="number" class="form-control @error('age') is-invalid @enderror"
                                    id="attendee_age" name="age" value="{{ old('age') }}" min="18"
                                    max="120" required>
                                @error('age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You must be at least 18 years old</small>
                            </div>

                            <div class="mb-3">
                                <label for="attendee_password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="attendee_password" name="password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="password-strength mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">Must be 8+ chars with uppercase, number & symbol</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="attendee_password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="attendee_password_confirmation"
                                    name="password_confirmation" required>
                                <div class="password-match-feedback"></div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
                                        name="terms" id="attendee_terms" {{ old('terms') ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="attendee_terms">
                                        I agree to the <a href="#" target="_blank">Terms of Service</a> and
                                        <a href="#" target="_blank">Privacy Policy</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Create Attendee Account
                                </button>
                            </div>
                        </form>

                        <!-- Event Creator Registration Form -->
                        <form method="POST" action="{{ route('register') }}" id="creatorForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="user_type" value="event_creator">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="creator_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        id="creator_first_name" name="first_name" value="{{ old('first_name') }}"
                                        required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="creator_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        id="creator_last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="creator_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="creator_email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="creator_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        id="creator_phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="creator_organization" class="form-label">Organization/Business
                                        Name</label>
                                    <input type="text"
                                        class="form-control @error('organization_name') is-invalid @enderror"
                                        id="creator_organization" name="organization_name"
                                        value="{{ old('organization_name') }}" required>
                                    @error('organization_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="creator_tax_id" class="form-label">Tax ID / Business Registration
                                    (Optional)</label>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                    id="creator_tax_id" name="tax_id" value="{{ old('tax_id') }}">
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="creator_password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="creator_password" name="password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="creator_password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="creator_password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Event creator accounts require admin approval before you can start
                                creating events.
                                You'll receive an email once your account is approved.
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
                                        name="terms" id="creator_terms" {{ old('terms') ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="creator_terms">
                                        I agree to the <a href="#" target="_blank">Terms of Service</a> and
                                        <a href="#" target="_blank">Privacy Policy</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    Submit for Approval
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .user-type-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
        }

        .user-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .user-type-card.selected {
            border-color: #0d6efd;
            background-color: #f8f9ff;
        }

        .user-type-card.creator-card.selected {
            border-color: #198754;
            background-color: #f1f9f1;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let selectedType = null;

            // User type selection
            $('.user-type-card').click(function() {
                selectedType = $(this).data('type');

                $('.user-type-card').removeClass('selected');
                $(this).addClass('selected');

                $('#userTypeSelection').slideUp();
                $('#registrationForms').slideDown();

                if (selectedType === 'attendee') {
                    $('#formTitle').text('Create Attendee Account');
                    $('#attendeeForm').show();
                    $('#creatorForm').hide();
                } else {
                    $('#formTitle').text('Create Event Creator Account');
                    $('#creatorForm').show();
                    $('#attendeeForm').hide();
                }
            });

            // Back to type selection
            $('#backToType').click(function() {
                $('#registrationForms').slideUp();
                $('#userTypeSelection').slideDown();
                $('.user-type-card').removeClass('selected');
                $('#attendeeForm').hide();
                $('#creatorForm').hide();
            });

            // Password strength checker
            $('#attendee_password, #creator_password').on('input', function() {
                const password = $(this).val();
                const strength = calculatePasswordStrength(password);
                const progressBar = $(this).closest('form').find('.password-strength .progress-bar');

                progressBar.css('width', strength.score + '%');
                progressBar.removeClass('bg-danger bg-warning bg-info bg-success');

                if (strength.score < 25) progressBar.addClass('bg-danger');
                else if (strength.score < 50) progressBar.addClass('bg-warning');
                else if (strength.score < 75) progressBar.addClass('bg-info');
                else progressBar.addClass('bg-success');
            });

            // Password match checker
            $('#attendee_password_confirmation, #creator_password_confirmation').on('input', function() {
                const form = $(this).closest('form');
                const password = form.find('input[name="password"]').val();
                const confirm = $(this).val();
                const feedback = form.find('.password-match-feedback');

                if (confirm.length > 0) {
                    if (password === confirm) {
                        feedback.html(
                            '<small class="text-success"><i class="fas fa-check"></i> Passwords match</small>'
                        );
                    } else {
                        feedback.html(
                            '<small class="text-danger"><i class="fas fa-times"></i> Passwords do not match</small>'
                        );
                    }
                } else {
                    feedback.html('');
                }
            });

            function calculatePasswordStrength(password) {
                let score = 0;
                if (password.length >= 8) score += 25;
                if (password.match(/[a-z]/)) score += 25;
                if (password.match(/[A-Z]/)) score += 25;
                if (password.match(/[0-9]/)) score += 12.5;
                if (password.match(/[^a-zA-Z0-9]/)) score += 12.5;
                return {
                    score
                };
            }

            // Toggle password visibility
            $('.toggle-password').click(function() {
                const input = $(this).closest('.input-group').find('input');
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });
    </script>
@endpush
