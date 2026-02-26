<?php

namespace App\Modules\Registration\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ApiProfileController
{
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'first_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'username' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user->fresh(),
            'message' => 'Profile updated.',
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['required', 'file', 'image', 'max:4096'],
        ]);

        /** @var UploadedFile $file */
        $file = $validated['avatar'];

        $path = $file->store('avatars', ['disk' => 'public']);

        $user = $request->user();
        $user->profile?->update(['profile_photo' => $path]);

        return response()->json([
            'success' => true,
            'data' => ['path' => $path, 'url' => Storage::disk('public')->url($path)],
            'message' => 'Avatar uploaded.',
        ]);
    }
}
