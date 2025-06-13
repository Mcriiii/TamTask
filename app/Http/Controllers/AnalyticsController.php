<?php

namespace App\Http\Controllers;

use App\Models\LostFound;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function lostFoundReport(Request $request)
    {
        $month = $request->month;
        $from = $request->from;
        $to = $request->to;
        $year = $request->year;

        // Priority: Month > Range > Year > Default
        if ($month) {
            $fromDate = date('Y-m-01', strtotime($month));
            $toDate = date('Y-m-t', strtotime($month));
        } elseif ($from && $to) {
            $fromDate = $from;
            $toDate = $to;
        } elseif ($year) {
            $fromDate = $year . '-01-01';
            $toDate = $year . '-12-31';
        } else {
            $fromDate = now()->startOfYear()->toDateString(); 
            $toDate = now()->endOfYear()->toDateString();     
        }

        // LOST & FOUND: Filtered query
        $lfQuery = LostFound::query()->whereBetween('date_reported', [$fromDate, $toDate]);

        // Lost & Found grouped data for chart
        $lfData = $lfQuery->select('item_type', DB::raw('count(*) as total'))
            ->groupBy('item_type')
            ->orderByDesc('total')
            ->get();
        $labels = $lfData->pluck('item_type');
        $counts = $lfData->pluck('total');
        $maxCount = $lfData->max('total');
        $topItems = $lfData->filter(fn($item) => $item->total == $maxCount);
        $topItem = $topItems->count() === 1 ? $topItems->first() : null;

        // KPI Queries
        $totalLost = LostFound::whereBetween('date_reported', [$fromDate, $toDate])->count();
        $totalViolations = Violation::whereBetween('date_reported', [$fromDate, $toDate])->count();
        $totalClaimed = LostFound::where('status', 'Claimed')->whereBetween('date_reported', [$fromDate, $toDate])->count();
        $totalUnclaimed = LostFound::where('status', 'Unclaimed')->whereBetween('date_reported', [$fromDate, $toDate])->count();

        // VIOLATION CHART DATA
        $violationData = Violation::whereBetween('date_reported', [$fromDate, $toDate])
            ->select('offense', DB::raw('count(*) as total'))
            ->groupBy('offense')
            ->orderByDesc('total')
            ->get();
        $vLabels = $violationData->pluck('offense');
        $vCounts = $violationData->pluck('total');

        // Most recent 5 entries (not filtered)
        $recent = LostFound::latest()->take(5)->get();
        $recentViolations = Violation::latest()->take(5)->get();

        // Dashboard view per role
        $view = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'dashboard';

        return view($view, compact(
            'labels',
            'counts',
            'topItem',
            'recent',
            'vLabels',
            'vCounts',
            'recentViolations',
            'totalLost',
            'totalViolations',
            'totalClaimed',
            'totalUnclaimed',
            'month',
            'from',
            'to',
            'year'
        ));
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

    // VIOLATIONS


}
