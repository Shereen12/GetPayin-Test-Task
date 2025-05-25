<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get the user's profile information.
     */
    public function show(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
            'meta' => [
                'current_time' => '2025-05-22 06:44:08',
                'current_user' => 'Shereen12'
            ]
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user,
            'meta' => [
                'current_time' => '2025-05-22 06:44:08',
                'current_user' => 'Shereen12'
            ]
        ]);
    }

}