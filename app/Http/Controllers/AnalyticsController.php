<?php

namespace App\Http\Controllers;

use App\Models\LostFound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function lostFoundReport(Request $request)
    {
        $month = $request->month;
        $query = LostFound::query();

        if ($month) {
            $query->whereMonth('date_reported', '=', date('m', strtotime($month)))
                  ->whereYear('date_reported', '=', date('Y', strtotime($month)));
        }

        // Aggregated data for the chart
        $data = $query->select('item_type', DB::raw('count(*) as total'))
                      ->groupBy('item_type')
                      ->orderByDesc('total')
                      ->get();

        $labels = $data->pluck('item_type');
        $counts = $data->pluck('total');
        $topItem = $data->first();

        // Show recent entries regardless of filter
        $recent = LostFound::latest()->take(5)->get();

        // 👉 Dynamic view based on role
        $user = auth()->user();
        $view = $user->role === 'admin' ? 'admin.dashboard' : 'dashboard';

        return view($view, compact('labels', 'counts', 'topItem', 'recent', 'month'));
    }

    public function exportToPdf(Request $request)
    {
        $month = $request->month;
        $itemType = $request->item_type;
        $location = $request->location_found;
        $reporter = $request->reporter_name;

        // Query for summary aggregation
        $query = LostFound::query();

        if ($month) {
            $query->whereMonth('date_reported', date('m', strtotime($month)))
                  ->whereYear('date_reported', date('Y', strtotime($month)));
        }
        if ($itemType) {
            $query->where('item_type', $itemType);
        }
        if ($location) {
            $query->where('location_found', 'like', "%{$location}%");
        }
        if ($reporter) {
            $query->where('reporter_name', 'like', "%{$reporter}%");
        }

        // Grouped item types
        $data = $query->select('item_type', DB::raw('count(*) as total'))
                      ->groupBy('item_type')
                      ->orderByDesc('total')
                      ->get();

        // Grouped status counts
        $statusSummary = $query->select('status', DB::raw('count(*) as total'))
                               ->groupBy('status')
                               ->orderBy('status')
                               ->get();

        // Re-run full query to fetch full record fields
        $entryQuery = LostFound::query();

        if ($month) {
            $entryQuery->whereMonth('date_reported', date('m', strtotime($month)))
                       ->whereYear('date_reported', date('Y', strtotime($month)));
        }
        if ($itemType) {
            $entryQuery->where('item_type', $itemType);
        }
        if ($location) {
            $entryQuery->where('location_found', 'like', "%{$location}%");
        }
        if ($reporter) {
            $entryQuery->where('reporter_name', 'like', "%{$reporter}%");
        }

        $entries = $entryQuery->orderByDesc('date_reported')->get();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.export', [
            'data' => $data,
            'entries' => $entries,
            'statusSummary' => $statusSummary,
            'month' => $month,
            'itemType' => $itemType,
            'location' => $location,
            'reporter' => $reporter
        ]);

        return $pdf->download('Analytics.pdf');
    }
}
