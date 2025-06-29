<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LostFound;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");;
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        $reports = $query->latest()->paginate(10);
        $itemTypes = LostFound::select('item_type')->distinct()->pluck('item_type');

        $view = Auth::user()->role === 'admin' ? 'admin.lostandfound' : 'lostandfound';
        return view($view, compact('reports', 'itemTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_name' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => 'required|email|max:255',
            'date_reported' => 'required|date',
            'location_found' => 'nullable|string|max:255',
            'item_type' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'custom_item_type' => 'required|required_if:item_type,Others|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('_modal', 'add');
        }

        $prefix = $request->report_type === 'FND' ? 'FND' : 'LOS';
        do {
            $ticketNo = $prefix . '-' . rand(1000, 9999);
        } while (LostFound::where('ticket_no', $ticketNo)->exists());

        $itemType = $request->item_type === 'Others' && $request->filled('custom_item_type')
            ? $request->custom_item_type
            : $request->item_type;

        LostFound::create([
            'ticket_no' => $ticketNo,
            'reporter_name' => $request->reporter_name,
            'email' => $request->email,
            'date_reported' => $request->date_reported,
            'location_found' => $request->location_found,
            'item_type' => $itemType,
            'description' => $request->description,
            'status' => $request->report_type === 'FND' ? 'Unclaimed' : 'Searching',
        ]);

        return redirect()->route($this->getRoutePrefix() . 'lost-found.index')
            ->with('success', 'Report submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $report = LostFound::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'reporter_name' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => 'required|email|max:255',
            'date_reported' => 'required|date',
            'location_found' => 'nullable|string|max:255',
            'item_type' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'custom_item_type' => 'required_if:item_type,Others|nullable|string|max:100',
            'status' => 'required|in:Item Stored,Claimed,Unclaimed,Searching,Found,Returned,Closed,Disposed',

        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('_modal', 'edit')
                ->with('edit_id', $id);
        }

        $data = $validator->validated();
        $data['item_type'] = $request->item_type === 'Others' && $request->filled('custom_item_type')
            ? $request->custom_item_type
            : $request->item_type;

        $report->update($data);

        return redirect()->route($this->getRoutePrefix() . 'lost-found.index')
            ->with('success', 'Report updated successfully.');
    }

    public function destroy($id)
    {
        $report = LostFound::findOrFail($id);
        $report->delete();

        return redirect()->route($this->getRoutePrefix() . 'lost-found.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function markAsClaimed($id)
    {
        $report = LostFound::findOrFail($id);
        $report->update(['status' => 'Claimed']);

        return redirect()->route($this->getRoutePrefix() . 'lost-found.index')
            ->with('success', 'Item marked as claimed.');
    }

    protected function getRoutePrefix()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin' ? 'admin.' : '';
    }

    public function exportPdf(Request $request)
    {
        $query = LostFound::query();

        // Optional month filter (expects YYYY-MM from <input type="month">)
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereMonth('date_reported', $month->month)
                ->whereYear('date_reported', $month->year);
        }

        // Optional year-only filter
        if ($request->filled('year')) {
            $query->whereYear('date_reported', $request->year);
        }

        // Get filtered data
        $reports = $query->latest()->get();

        // Summary counts
        $total = $reports->count();
        $claimed = $reports->where('status', 'Claimed')->count();
        $unclaimed = $reports->where('status', 'Unclaimed')->count();

        // 🥇 Get the top most lost item
        $itemCounts = $reports->groupBy('item_type')->map->count();

        $max = $itemCounts->max();
        $topItems = $itemCounts->filter(fn($count) => $count === $max);

        $topItem = $topItems->count() === 1
            ? ['item' => $topItems->keys()->first(), 'count' => $max]
            : null;

        // Load the PDF view with all data
        $pdf = Pdf::loadView('admin.pdf.lostfound', [
            'reports' => $reports,
            'total' => $total,
            'claimed' => $claimed,
            'unclaimed' => $unclaimed,
            'topItem' => $topItem, // 🟢 pass this to the view
        ])->setPaper('a4', 'portrait');

        return $pdf->download('lost_found_reports.pdf');
    }
}
