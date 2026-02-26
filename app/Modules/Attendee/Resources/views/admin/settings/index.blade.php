@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Attendee Module Settings')

@section('breadcrumb')
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Module Configuration</h3>
            </div>
            <form action="{{ route('admin.attendee.settings.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                <i class="fas fa-cog"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="booking-tab" data-toggle="tab" href="#booking" role="tab">
                                <i class="fas fa-ticket-alt"></i> Booking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab">
                                <i class="fas fa-credit-card"></i> Payment
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ticket-tab" data-toggle="tab" href="#ticket" role="tab">
                                <i class="fas fa-qrcode"></i> Ticket
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab">
                                <i class="fas fa-envelope"></i> Email
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-3" id="settingsTabContent">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <h5 class="mb-3">General Settings</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Module Name</label>
                                        <input type="text" name="module_name" class="form-control" 
                                               value="{{ $settings['module_name'] ?? 'Attendee Module' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Currency</label>
                                        <select name="currency" class="form-control">
                                            <option value="USD" {{ ($settings['currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                            <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                            <option value="GBP" {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                            <option value="JPY" {{ ($settings['currency'] ?? '') == 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date Format</label>
                                        <select name="date_format" class="form-control">
                                            <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                            <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                            <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                            <option value="F j, Y" {{ ($settings['date_format'] ?? '') == 'F j, Y' ? 'selected' : '' }}>Month DD, YYYY</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Time Format</label>
                                        <select name="time_format" class="form-control">
                                            <option value="H:i" {{ ($settings['time_format'] ?? '') == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                            <option value="h:i A" {{ ($settings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12 Hour (02:30 PM)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Settings -->
                        <div class="tab-pane fade" id="booking" role="tabpanel">
                            <h5 class="mb-3">Booking Settings</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Booking Number Prefix</label>
                                        <input type="text" name="booking_prefix" class="form-control" 
                                               value="{{ $settings['booking_prefix'] ?? 'BKG' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Max Tickets Per Order</label>
                                        <input type="number" name="max_per_order" class="form-control" 
                                               value="{{ $settings['max_per_order'] ?? 10 }}" min="1" max="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Booking Expiry (hours)</label>
                                        <input type="number" name="expiry_hours" class="form-control" 
                                               value="{{ $settings['expiry_hours'] ?? 24 }}" min="1" max="168">
                                        <small class="text-muted">Pending bookings expire after X hours</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cancellation Cutoff (hours)</label>
                                        <input type="number" name="cancellation_cutoff" class="form-control" 
                                               value="{{ $settings['cancellation_cutoff'] ?? 48 }}" min="0" max="720">
                                        <small class="text-muted">Hours before event when cancellation is not allowed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="auto_confirm_free" class="custom-control-input" id="auto_confirm_free" value="1" 
                                                   {{ ($settings['auto_confirm_free'] ?? true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="auto_confirm_free">Auto-confirm free bookings</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="require_login" class="custom-control-input" id="require_login" value="1" 
                                                   {{ ($settings['require_login'] ?? true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="require_login">Require login to book</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div class="tab-pane fade" id="payment" role="tabpanel">
                            <h5 class="mb-3">Payment Settings</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Tax Rate (%)</label>
                                        <input type="number" name="tax_rate" class="form-control" 
                                               value="{{ $settings['tax_rate'] ?? 0 }}" min="0" max="100" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Service Fee</label>
                                        <input type="number" name="service_fee" class="form-control" 
                                               value="{{ $settings['service_fee'] ?? 0 }}" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="stripe_enabled" class="custom-control-input" id="stripe_enabled" value="1" 
                                                   {{ ($settings['stripe_enabled'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="stripe_enabled">Enable Stripe</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="paypal_enabled" class="custom-control-input" id="paypal_enabled" value="1" 
                                                   {{ ($settings['paypal_enabled'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="paypal_enabled">Enable PayPal</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="bank_transfer_enabled" class="custom-control-input" id="bank_transfer_enabled" value="1" 
                                                   {{ ($settings['bank_transfer_enabled'] ?? true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="bank_transfer_enabled">Enable Bank Transfer</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Settings -->
                        <div class="tab-pane fade" id="ticket" role="tabpanel">
                            <h5 class="mb-3">Ticket Settings</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ticket Number Prefix</label>
                                        <input type="text" name="ticket_prefix" class="form-control" 
                                               value="{{ $settings['ticket_prefix'] ?? 'TIC' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>QR Code Size (px)</label>
                                        <input type="number" name="qr_size" class="form-control" 
                                               value="{{ $settings['qr_size'] ?? 200 }}" min="100" max="500">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ticket Format</label>
                                        <select name="ticket_format" class="form-control">
                                            <option value="pdf" {{ ($settings['ticket_format'] ?? 'pdf') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                            <option value="html" {{ ($settings['ticket_format'] ?? '') == 'html' ? 'selected' : '' }}>HTML</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div class="tab-pane fade" id="email" role="tabpanel">
                            <h5 class="mb-3">Email Settings</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From Email</label>
                                        <input type="email" name="from_email" class="form-control" 
                                               value="{{ $settings['from_email'] ?? config('mail.from.address') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From Name</label>
                                        <input type="text" name="from_name" class="form-control" 
                                               value="{{ $settings['from_name'] ?? config('mail.from.name') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirmation Email Subject</label>
                                        <input type="text" name="confirmation_subject" class="form-control" 
                                               value="{{ $settings['confirmation_subject'] ?? 'Booking Confirmation' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Reminder Hours Before Event</label>
                                        <input type="number" name="reminder_hours" class="form-control" 
                                               value="{{ $settings['reminder_hours'] ?? 24 }}" min="1" max="168">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection