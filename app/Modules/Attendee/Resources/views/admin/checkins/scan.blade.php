@extends('attendee::admin.layouts.attendee')

@section('page-title', 'QR Code Scanner')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.checkins.index') }}">Check-ins</a></li>
    <li class="breadcrumb-item active">Scanner</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-qrcode mr-2"></i> QR Code Scanner
                </h3>
            </div>
            <div class="card-body">
                <!-- Scanner Selection -->
                <div class="text-center mb-4">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active">
                            <input type="radio" name="scanner_type" id="camera" value="camera" checked> 
                            <i class="fas fa-camera"></i> Camera
                        </label>
                        <label class="btn btn-outline-secondary">
                            <input type="radio" name="scanner_type" id="manual" value="manual"> 
                            <i class="fas fa-keyboard"></i> Manual Entry
                        </label>
                    </div>
                </div>

                <!-- Camera Scanner -->
                <div id="camera-scanner" class="scanner-section">
                    <div class="text-center mb-3">
                        <video id="scanner" width="100%" height="400" style="border: 2px solid #ddd; border-radius: 10px;"></video>
                    </div>
                    <p class="text-muted text-center">
                        <i class="fas fa-info-circle"></i> 
                        Point your camera at a QR code to scan
                    </p>
                </div>

                <!-- Manual Entry -->
                <div id="manual-scanner" class="scanner-section" style="display: none;">
                    <div class="form-group">
                        <label for="manualCode">Enter Ticket Number or QR Code</label>
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" id="manualCode" 
                                   placeholder="e.g., TIC123456789 or QR123456789">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="manualCheck">
                                    <i class="fas fa-search"></i> Verify
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">You can enter ticket number or QR code manually</small>
                    </div>
                </div>

                <!-- Result Card -->
                <div id="resultCard" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header" id="resultHeader">
                            <h5 class="mb-0" id="resultTitle"></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-sm" id="resultDetails">
                                        <!-- Details will be populated here -->
                                    </table>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div id="qrDisplay"></div>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-success btn-lg" id="confirmCheckin" style="display: none;">
                                    <i class="fas fa-check-circle"></i> Confirm Check-in
                                </button>
                                <button class="btn btn-primary btn-lg" id="scanAgain" style="display: none;">
                                    <i class="fas fa-redo"></i> Scan Again
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Scanner library -->
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('attendee-scripts')
<script>
$(document).ready(function() {
    let html5QrCode;
    let currentTicket = null;
    
    // Toggle between camera and manual
    $('input[name="scanner_type"]').change(function() {
        const type = $(this).val();
        
        // Hide all sections
        $('.scanner-section').hide();
        
        // Show selected section
        $('#' + type + '-scanner').show();
        
        // Stop camera if switching away from it
        if (type !== 'camera' && html5QrCode) {
            html5QrCode.stop();
        }
        
        // Start camera if switching to it
        if (type === 'camera') {
            startScanner();
        }
        
        // Hide result when switching
        $('#resultCard').hide();
        currentTicket = null;
    });
    
    // Start camera scanner
    function startScanner() {
        if (html5QrCode) {
            html5QrCode.stop();
        }
        
        html5QrCode = new Html5Qrcode("scanner");
        
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };
        
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                // Stop scanning
                html5QrCode.stop();
                // Process the QR code
                verifyTicket(decodedText);
            },
            (error) => {
                // Scanning error - ignore
            }
        ).catch((err) => {
            Swal.fire('Camera Error', 'Could not access camera. Please use manual entry.', 'error');
            $('#manual').prop('checked', true).trigger('change');
        });
    }
    
    // Start camera on page load
    startScanner();
    
    // Manual verification
    $('#manualCheck').click(function() {
        let code = $('#manualCode').val().trim();
        if (code) {
            verifyTicket(code);
        } else {
            Swal.fire('Warning', 'Please enter a ticket number or QR code', 'warning');
        }
    });
    
    $('#manualCode').keypress(function(e) {
        if (e.which == 13) {
            $('#manualCheck').click();
        }
    });
    
    // Verify ticket function
    function verifyTicket(code) {
        $.ajax({
            url: '{{ route("api.attendee.checkin.scan") }}',
            method: 'POST',
            data: {
                qr_code: code,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showTicketDetails(response.data);
                } else {
                    showError(response.message || 'Invalid ticket');
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Failed to verify ticket';
                showError(message);
            }
        });
    }
    
    // Show ticket details
    function showTicketDetails(data) {
        currentTicket = data.ticket;
        $('#resultCard').show();
        $('#scanAgain').hide();
        
        if (data.ticket.checked_in) {
            // Already checked in
            $('#resultHeader').removeClass().addClass('card-header bg-warning text-white');
            $('#resultTitle').html('<i class="fas fa-exclamation-triangle"></i> Already Checked In');
            
            let details = `
                <tr><th width="200">Ticket Number:</th><td><strong>${data.ticket.ticket_number}</strong></td></tr>
                <tr><th>Attendee:</th><td>${data.ticket.attendee_name}</td></tr>
                <tr><th>Email:</th><td>${data.ticket.attendee_email}</td></tr>
                <tr><th>Event:</th><td>${data.ticket.booking.event.title}</td></tr>
                <tr><th>Event Date:</th><td>${new Date(data.ticket.booking.event.start_date).toLocaleString()}</td></tr>
                <tr><th>Checked In At:</th><td>${new Date(data.ticket.checked_in_at).toLocaleString()}</td></tr>
            `;
            
            $('#resultDetails').html(details);
            $('#confirmCheckin').hide();
            $('#scanAgain').show();
            
        } else if (!data.can_check_in) {
            // Cannot check in (wrong date, etc)
            $('#resultHeader').removeClass().addClass('card-header bg-danger text-white');
            $('#resultTitle').html('<i class="fas fa-times-circle"></i> Cannot Check In');
            
            let details = `
                <tr><th width="200">Ticket Number:</th><td><strong>${data.ticket.ticket_number}</strong></td></tr>
                <tr><th>Attendee:</th><td>${data.ticket.attendee_name}</td></tr>
                <tr><th>Email:</th><td>${data.ticket.attendee_email}</td></tr>
                <tr><th>Event:</th><td>${data.ticket.booking.event.title}</td></tr>
                <tr><th>Event Date:</th><td>${new Date(data.ticket.booking.event.start_date).toLocaleString()}</td></tr>
                <tr><th>Reason:</th><td class="text-danger">Check-in not available at this time</td></tr>
            `;
            
            $('#resultDetails').html(details);
            $('#confirmCheckin').hide();
            $('#scanAgain').show();
            
        } else {
            // Valid ticket
            $('#resultHeader').removeClass().addClass('card-header bg-success text-white');
            $('#resultTitle').html('<i class="fas fa-check-circle"></i> Valid Ticket');
            
            let details = `
                <tr><th width="200">Ticket Number:</th><td><strong>${data.ticket.ticket_number}</strong></td></tr>
                <tr><th>Attendee:</th><td>${data.ticket.attendee_name}</td></tr>
                <tr><th>Email:</th><td>${data.ticket.attendee_email}</td></tr>
                <tr><th>Event:</th><td>${data.ticket.booking.event.title}</td></tr>
                <tr><th>Event Date:</th><td>${new Date(data.ticket.booking.event.start_date).toLocaleString()}</td></tr>
                <tr><th>Venue:</th><td>${data.ticket.booking.event.venue}</td></tr>
            `;
            
            $('#resultDetails').html(details);
            $('#confirmCheckin').show();
            $('#scanAgain').hide();
        }
        
        // Show QR code if available
        if (data.ticket.qr_code_url) {
            $('#qrDisplay').html(`<img src="${data.ticket.qr_code_url}" alt="QR Code" style="width: 150px;">`);
        }
    }
    
    // Show error
    function showError(message) {
        $('#resultCard').show();
        $('#resultHeader').removeClass().addClass('card-header bg-danger text-white');
        $('#resultTitle').html('<i class="fas fa-times-circle"></i> Error');
        $('#resultDetails').html(`<tr><td colspan="2" class="text-center text-danger">${message}</td></tr>`);
        $('#confirmCheckin').hide();
        $('#scanAgain').show();
        currentTicket = null;
    }
    
    // Confirm check-in
    $('#confirmCheckin').click(function() {
        if (!currentTicket) return;
        
        Swal.fire({
            title: 'Confirm Check-in',
            text: `Check in ${currentTicket.attendee_name}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, check in',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("api.attendee.checkin.process") }}',
                    method: 'POST',
                    data: {
                        ticket_id: currentTicket.id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Attendee checked in successfully',
                                icon: 'success',
                                timer: 2000
                            }).then(() => {
                                resetScanner();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Check-in failed', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Check-in failed', 'error');
                    }
                });
            }
        });
    });
    
    // Scan again
    $('#scanAgain').click(function() {
        resetScanner();
    });
    
    // Reset scanner
    function resetScanner() {
        $('#resultCard').hide();
        $('#manualCode').val('');
        currentTicket = null;
        
        const scannerType = $('input[name="scanner_type"]:checked').val();
        if (scannerType === 'camera') {
            startScanner();
        }
    }
});
</script>
@endpush