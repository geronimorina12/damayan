<?php

namespace App\Http\Controllers;

use App\Models\ArchiveContributions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveContribution extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'member_id' => 'required|exists:members,id',
            'deceased_id' => 'nullable|exists:deceased,id',
            'collector' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'purok' => 'nullable|in:purok1,purok2,purok3,purok4',
            'status' => 'nullable|in:paid,not_paid',
        ]);

        $validatedData['updated_by'] = Auth::id();

        ArchiveContributions::create($validatedData);

        return response()->json([
            'message' => 'Archived contribution added successfully!'
        ], 201);
    }
}
