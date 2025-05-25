<?php

namespace App\Http\Controllers\Api;

use App\Models\Platform;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::all();
        return response()->json($platforms);
    }

    public function toggleActiveForUser(Request $request, $platformId)
    {
        $user = $request->user();

        // Toggle the active status for the user on the specified platform
        if ($user->platforms()->where('platform_id', $platformId)->exists()) {
            $user->platforms()->detach($platformId);
        } else {
            $user->platforms()->attach($platformId);
        }

        return response()->json([
            'message' => 'Platform active status toggled successfully.',
            'active' => $user->platforms()->where('platform_id', $platformId)->exists(),
        ]);
    }
}