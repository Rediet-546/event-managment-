@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Upload -->
                        <div class="text-center mb-4">
                            <div class="avatar-wrapper position-relative d-inline-block">
                                <img src="{{ $user->profile->avatar_url ?? asset('images/default-avatar.png') }}" 
                                     alt="Avatar" 
                                     class="rounded-circle border border-4 border-primary"
                                     id="avatarPreview"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <label for="avatar" class="avatar-upload-btn">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                            </div>
                            <p class="text-muted small mt-2">Click the camera icon to change photo</p>
                        </div>

                        <!-- Personal Information -->
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user text-primary me-2"></i>Personal Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name', $user->first_name) }}">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="{{ old('last_name', $user->last_name) }}">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', $user->username) }}">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-address-book text-primary me-2"></i>Contact Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->profile->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <select class="form-select @error('country') is-invalid @enderror" 
                                        id="country" 
                                        name="country">
                                    <option value="">Select Country</option>
                                    @foreach(['US' => 'United States', 'CA' => 'Canada', 'UK' => 'United Kingdom'] as $code => $name)
                                        <option value="{{ $code }}" 
                                            {{ old('country', $user->profile->country) == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address_line1" class="form-label">Address Line 1</label>
                            <input type="text" 
                                   class="form-control @error('address_line1') is-invalid @enderror" 
                                   id="address_line1" 
                                   name="address_line1" 
                                   value="{{ old('address_line1', $user->profile->address_line1) }}">
                            @error('address_line1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address_line2" class="form-label">Address Line 2</label>
                            <input type="text" 
                                   class="form-control @error('address_line2') is-invalid @enderror" 
                                   id="address_line2" 
                                   name="address_line2" 
                                   value="{{ old('address_line2', $user->profile->address_line2) }}">
                            @error('address_line2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city', $user->profile->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" 
                                       class="form-control @error('state') is-invalid @enderror" 
                                       id="state" 
                                       name="state" 
                                       value="{{ old('state', $user->profile->state) }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" 
                                       class="form-control @error('postal_code') is-invalid @enderror" 
                                       id="postal_code" 
                                       name="postal_code" 
                                       value="{{ old('postal_code', $user->profile->postal_code) }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" 
                                      name="bio" 
                                      rows="3">{{ old('bio', $user->profile->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Creator-specific fields -->
                        @if($user->isEventCreator())
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-building text-primary me-2"></i>Business Information
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="organization_name" class="form-label">Organization Name</label>
                                    <input type="text" 
                                           class="form-control @error('organization_name') is-invalid @enderror" 
                                           id="organization_name" 
                                           name="organization_name" 
                                           value="{{ old('organization_name', $user->organization_name) }}">
                                    @error('organization_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tax_id" class="form-label">Tax ID / Business Registration</label>
                                    <input type="text" 
                                           class="form-control @error('tax_id') is-invalid @enderror" 
                                           id="tax_id" 
                                           name="tax_id" 
                                           value="{{ old('tax_id', $user->tax_id) }}">
                                    @error('tax_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>

                    <!-- Danger Zone -->
                    <div class="mt-5 pt-3 border-top">
                        <h6 class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                        </h6>
                        
                        <!-- Change Password -->
                        <div class="card border-warning mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Change Password</h6>
                                <form action="{{ route('profile.password') }}" method="POST" class="row g-3">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="col-md-4">
                                        <input type="password" 
                                               class="form-control" 
                                               name="current_password" 
                                               placeholder="Current Password" 
                                               required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="password" 
                                               class="form-control" 
                                               name="new_password" 
                                               placeholder="New Password" 
                                               required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="password" 
                                               class="form-control" 
                                               name="new_password_confirmation" 
                                               placeholder="Confirm Password" 
                                               required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-warning w-100">
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Delete Account -->
                        <div class="card border-danger">
                            <div class="card-body">
                                <h6 class="card-title text-danger">Delete Account</h6>
                                <p class="card-text small text-muted">
                                    Once you delete your account, there is no going back. Please be certain.
                                </p>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                    <i class="fas fa-trash-alt me-2"></i>Delete My Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                <form action="{{ route('profile.destroy') }}" method="POST" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="delete_password" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                    Yes, Delete My Account
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Preview avatar before upload
    $('#avatar').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-wrapper {
    position: relative;
}
.avatar-upload-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #0d6efd;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 3px solid white;
    transition: all 0.3s;
}
.avatar-upload-btn:hover {
    background: #0b5ed7;
    transform: scale(1.1);
}
</style>
@endpush