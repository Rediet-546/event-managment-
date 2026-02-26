<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    // General Auth Messages
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'unauthorized' => 'You are not authorized to perform this action.',
    'forbidden' => 'You do not have permission to access this resource.',
    'not_found' => 'User not found.',
    'already_exists' => 'User already exists.',

    // Registration Messages
    'register_success' => 'Registration successful! Welcome :name!',
    'register_pending' => 'Registration submitted! Your :type account is pending approval.',
    'register_attendee_success' => 'Registration successful! Welcome :name! Start browsing events now.',
    'register_creator_pending' => 'Thank you for registering as an Event Creator! Your account is pending admin approval. You will receive an email once approved.',
    'register_failed' => 'Registration failed. Please try again.',
    'register_disabled' => 'Registration is currently disabled.',
    'creator_registration_disabled' => 'Event creator registration is currently disabled.',
    'terms_required' => 'You must accept the terms and conditions.',
    'age_restriction' => 'You must be at least :age years old to register.',
    'email_taken' => 'This email is already registered.',
    'username_taken' => 'This username is already taken.',

    // Login Messages
    'login_success' => 'Welcome back, :name!',
    'login_welcome_back' => 'Welcome back, :name! Ready for your next event?',
    'login_creator_welcome' => 'Welcome back, :name! Ready to manage your events?',
    'login_admin_welcome' => 'Welcome back, :name! You have administrator access.',
    'login_failed' => 'Invalid credentials.',
    'login_invalid' => 'The provided credentials do not match our records.',
    'login_inactive' => 'Your account has been deactivated. Please contact support.',
    'login_unverified' => 'Please verify your email address before logging in.',
    'login_pending_approval' => 'Your creator account is pending approval. You will receive an email once approved.',
    'login_locked' => 'Your account has been locked due to too many failed attempts. Please try again in :minutes minutes.',
    'logout_success' => 'You have been logged out successfully.',

    // Email Verification
    'email_verified' => 'Email verified successfully!',
    'email_already_verified' => 'Your email is already verified.',
    'email_verification_sent' => 'A new verification link has been sent to your email address.',
    'email_verification_failed' => 'Unable to verify email. The link may be invalid or expired.',
    'email_verification_required' => 'Please verify your email address to access this feature.',
    'email_verification_notice' => 'Before proceeding, please check your email for a verification link.',
    'email_verification_resend' => 'If you did not receive the email, click here to request another.',
    'invalid_verification_link' => 'Invalid verification link.',
    'verification_link_expired' => 'Verification link has expired. Please request a new one.',

    // Password Reset
    'password_reset_link_sent' => 'We have emailed your password reset link!',
    'password_reset_success' => 'Your password has been reset successfully!',
    'password_reset_token_invalid' => 'This password reset token is invalid.',
    'password_reset_token_expired' => 'Password reset token has expired. Please request a new one.',
    'password_reset_failed' => 'Unable to reset password. Please try again.',
    'password_changed' => 'Password changed successfully.',
    'password_incorrect' => 'Current password is incorrect.',
    'password_mismatch' => 'The provided passwords do not match.',
    'password_weak' => 'Please choose a stronger password.',
    'password_same' => 'New password cannot be the same as your current password.',

    // Profile Messages
    'profile_updated' => 'Profile updated successfully.',
    'profile_update_failed' => 'Unable to update profile. Please try again.',
    'profile_photo_updated' => 'Profile photo updated successfully.',
    'profile_photo_deleted' => 'Profile photo deleted successfully.',
    'account_deleted' => 'Your account has been deleted successfully.',
    'account_delete_failed' => 'Unable to delete account. Please try again.',
    'account_suspended' => 'Your account has been suspended. Please contact support.',
    'account_activated' => 'Account activated successfully.',
    'account_deactivated' => 'Account deactivated successfully.',

    // Creator Approval
    'creator_approved' => 'Creator account approved successfully!',
    'creator_approved_notification' => 'Your creator account has been approved! You can now create events.',
    'creator_rejected' => 'Creator application rejected.',
    'creator_rejected_notification' => 'Your creator application has been reviewed and was not approved at this time.',
    'creator_pending' => 'Your creator account is pending approval.',
    'creator_approval_pending' => 'This creator account is pending approval.',
    'creator_already_approved' => 'This creator account is already approved.',
    'creator_approval_failed' => 'Unable to process creator approval.',
    'creator_rejection_reason_required' => 'Please provide a reason for rejection.',
    'bulk_approve_success' => ':count creator accounts approved successfully.',

    // Role & Permission Messages
    'role_assigned' => 'Role assigned successfully.',
    'role_removed' => 'Role removed successfully.',
    'permission_granted' => 'Permission granted successfully.',
    'permission_revoked' => 'Permission revoked successfully.',
    'role_not_found' => 'Role not found.',
    'permission_not_found' => 'Permission not found.',
    'cannot_modify_super_admin' => 'Cannot modify super administrator role.',
    'cannot_delete_own_role' => 'Cannot remove your own administrator role.',

    // Session Messages
    'session_expired' => 'Your session has expired. Please login again.',
    'session_invalidated' => 'Session invalidated successfully.',
    'already_logged_in' => 'You are already logged in.',
    'already_logged_out' => 'You are already logged out.',
    'remember_me' => 'Remember me',

    // User Types
    'user_type' => [
        'attendee' => 'Event Attendee',
        'event_creator' => 'Event Creator',
        'admin' => 'Administrator',
        'super_admin' => 'Super Administrator',
        'pending_creator' => 'Pending Creator'
    ],

    // Form Labels
    'form' => [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'full_name' => 'Full Name',
        'username' => 'Username',
        'email' => 'Email Address',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'age' => 'Age',
        'phone' => 'Phone Number',
        'organization' => 'Organization Name',
        'tax_id' => 'Tax ID / Business Registration',
        'address' => 'Address',
        'city' => 'City',
        'state' => 'State/Province',
        'postal_code' => 'Postal Code',
        'country' => 'Country',
        'bio' => 'Bio',
        'terms' => 'I agree to the :terms and :privacy',
        'remember' => 'Remember Me',
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'submit' => 'Submit',
        'cancel' => 'Cancel',
        'save' => 'Save Changes',
        'update' => 'Update',
        'delete' => 'Delete',
        'search' => 'Search',
        'filter' => 'Filter',
        'clear' => 'Clear',
        'back' => 'Back',
        'continue' => 'Continue',
        'confirm' => 'Confirm',
        'choose_file' => 'Choose File',
        'upload' => 'Upload',
        'change_password' => 'Change Password',
        'forgot_password' => 'Forgot Your Password?',
        'reset_password' => 'Reset Password',
        'send_reset_link' => 'Send Password Reset Link'
    ],

    // Validation Messages
    'validation' => [
        'required' => 'The :attribute field is required.',
        'email' => 'Please enter a valid email address.',
        'unique' => 'This :attribute is already taken.',
        'min' => 'The :attribute must be at least :min characters.',
        'max' => 'The :attribute may not be greater than :max characters.',
        'confirmed' => 'The :attribute confirmation does not match.',
        'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
        'alpha' => 'The :attribute may only contain letters.',
        'numeric' => 'The :attribute must be a number.',
        'integer' => 'The :attribute must be an integer.',
        'age_min' => 'You must be at least :min years old.',
        'age_max' => 'Please enter a valid age.',
        'password_requirements' => 'Password must contain at least 8 characters, one uppercase letter, one number and one symbol.',
        'terms_accepted' => 'You must accept the terms and conditions.',
        'phone_format' => 'Please enter a valid phone number.',
        'tax_id_format' => 'Please enter a valid tax ID.'
    ],

    // Email Subjects
    'email' => [
        'welcome_subject' => 'Welcome to :app!',
        'welcome_attendee_subject' => 'Welcome to :app - Start Exploring Events!',
        'welcome_creator_subject' => 'Welcome to :app - Creator Account Pending',
        'password_reset_subject' => 'Reset Your Password',
        'verification_subject' => 'Verify Your Email Address',
        'creator_approved_subject' => 'Your Creator Account Has Been Approved!',
        'creator_rejected_subject' => 'Update on Your Creator Application',
        'pending_reminder_subject' => 'Your Creator Account is Still Pending',
        'booking_confirmation_subject' => 'Booking Confirmation - :event',
        'payment_receipt_subject' => 'Payment Receipt - :event',
        'event_reminder_subject' => 'Reminder: :event starts tomorrow!'
    ],

    // Notifications
    'notifications' => [
        'welcome_attendee' => 'Welcome to our community! Start browsing events now.',
        'welcome_creator' => 'Thank you for registering as a creator. We\'ll review your application shortly.',
        'pending_approval' => 'Your application is being reviewed by our team.',
        'approved' => 'Congratulations! Your creator account has been approved.',
        'rejected' => 'Thank you for your interest. Unfortunately, your application was not approved at this time.',
        'reminder' => 'Just a reminder that your application is still pending review.'
    ],

    // Time
    'time' => [
        'just_now' => 'Just now',
        'minutes_ago' => ':minutes minutes ago',
        'hours_ago' => ':hours hours ago',
        'yesterday' => 'Yesterday',
        'days_ago' => ':days days ago',
        'weeks_ago' => ':weeks weeks ago',
        'months_ago' => ':months months ago',
        'years_ago' => ':years years ago'
    ],

    // Errors
    'errors' => [
        'title' => 'Whoops! Something went wrong.',
        'default' => 'An error occurred. Please try again.',
        '404' => 'Page not found.',
        '403' => 'Access forbidden.',
        '500' => 'Server error. Please try again later.',
        'connection' => 'Connection error. Please check your internet connection.',
        'timeout' => 'Request timeout. Please try again.',
        'csrf' => 'Page expired. Please refresh and try again.'
    ],

    // Success Messages
    'success' => [
        'title' => 'Success!',
        'default' => 'Operation completed successfully.',
        'saved' => 'Changes saved successfully.',
        'deleted' => 'Item deleted successfully.',
        'updated' => 'Item updated successfully.',
        'created' => 'Item created successfully.',
        'sent' => 'Email sent successfully.'
    ],

    // Dashboard
    'dashboard' => [
        'attendee' => 'Attendee Dashboard',
        'creator' => 'Creator Dashboard',
        'admin' => 'Admin Dashboard',
        'welcome' => 'Welcome back, :name!',
        'statistics' => 'Statistics',
        'recent_activity' => 'Recent Activity',
        'upcoming_events' => 'Upcoming Events',
        'past_events' => 'Past Events',
        'total_bookings' => 'Total Bookings',
        'total_events' => 'Total Events',
        'total_revenue' => 'Total Revenue',
        'quick_actions' => 'Quick Actions'
    ],

    // Profile
    'profile' => [
        'title' => 'My Profile',
        'edit' => 'Edit Profile',
        'personal_info' => 'Personal Information',
        'contact_info' => 'Contact Information',
        'account_status' => 'Account Status',
        'member_since' => 'Member since',
        'last_login' => 'Last Login',
        'last_ip' => 'Last IP',
        'verified' => 'Verified',
        'unverified' => 'Unverified',
        'active' => 'Active',
        'inactive' => 'Inactive'
    ]
];