<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LostFound;

class LostFoundController extends Controller
{
    public function showLostandfound(Request $request)
    {
        $query = LostFound::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reporter_name', 'like', "%{$search}%")
                  ->orWhere('ticket_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        $reports = $query->latest()->paginate(10);
        $itemTypes = LostFound::select('item_type')->distinct()->pluck('item_type');

        return view('lostandfound', compact('reports', 'itemTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reporter_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_reported' => 'required|date',
            'location_found' => 'nullable|string|max:255',
            'item_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $prefix = $request->report_type === 'FND' ? 'FND' : 'LOS';
        $ticketNo = $prefix . '-' . rand(1000, 9999);

        LostFound::create([
            'ticket_no' => $ticketNo,
            'reporter_name' => $request->reporter_name,
            'email' => $request->email,
            'date_reported' => $request->date_reported,
            'location_found' => $request->location_found,
            'item_type' => $request->item_type,
            'description' => $request->description,
            'status' => 'Unclaimed',
        ]);

        return redirect()->route('lost-found.index')->with('success', 'Report submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $report = LostFound::findOrFail($id);

        $request->validate([
            'reporter_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'email' => 'nullable|email|max:255',
            'date_reported' => 'required|date',
            'location_found' => 'nullable|string|max:255',
            'item_type' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:Claimed,Unclaimed',
        ]);

        $report->update($request->all());

        return redirect()->route('lost-found.index')->with('success', 'Report updated successfully.');
    }

    public function destroy($id)
    {
        $report = LostFound::findOrFail($id);
        $report->delete();

        return redirect()->route('lost-found.index')->with('success', 'Report deleted successfully.');
    }

    public function markAsClaimed($id)
    {
        $report = LostFound::findOrFail($id);
        $report->update(['status' => 'Claimed']);

        return redirect()->route('lost-found.index')->with('success', 'Item marked as claimed.');
    }
}
