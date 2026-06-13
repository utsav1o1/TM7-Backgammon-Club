<?php

namespace App\Http\Controllers;

use App\Models\TeammateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeammateRequestController extends Controller
{
    public function index()
    {
        $requests = TeammateRequest::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Public/FindTeammate', [
            'requests' => $requests
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'players_needed' => 'required|string|max:50',
            'message' => 'nullable|string|max:500',
        ]);

        $validated['status'] = 'active';

        // If email already posted a request, update it
        $existing = TeammateRequest::where('email', $validated['email'])
            ->where('status', 'active')
            ->first();

        if ($existing) {
            $existing->update($validated);
        } else {
            TeammateRequest::create($validated);
        }

        return redirect()->back(); // Flash success via standard frontend
    }
}
