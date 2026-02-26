<?php

namespace App\Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Registration\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Show email verification notice
     */
    public function showNotice()
    {
        return view('registration::verify-email-notice');
    }

    /**
     * Verify email
     */
    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('info', 'Email already verified.');
        }

        $user->markEmailAsVerified();

        activity()
            ->performedOn($user)
            ->log('Email verified');

        return redirect()->route('login')
            ->with('success', 'Email verified successfully!');
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('info', 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent.');
    }

    /**
     * API: Verify email
     */
    public function apiVerify($id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification link'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified'
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }

    /**
     * API: Resend verification
     */
    public function apiResend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent'
        ]);
    }
}