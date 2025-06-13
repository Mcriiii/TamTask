@extends('layout.main')
@section('title', 'Dashboard')

@section('content')
@php
$prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
@endphp
<!-- Top Navbar -->
<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
    <div class="user-greeting">
        Hello, {{ Auth::user()->first_name }}
    </div>
</div>
<!-- 🌿 STYLES: Green BG + Glassmorphism -->
<style>
    .glass-card {
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }

    .glass-badge {
        background-color: rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(4px);
        border-radius: 1rem;
        padding: 4px 12px;
        font-size: 0.75rem;
        color: #000;
    }

    .fw-bold {
        font-weight: 600;
    }

    .kpi-box h4 {
        font-size: 1.4rem;
        margin: 0;
    }

    .recent-card {
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(6px);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    a {
        color: #0077cc;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<div class="main-wrapper">
    @include('layout.sidebar')
    <div class="main-content">


        <!-- Header & Filter -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">📊Analytics</h2>
            <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <div>
                    <label class="form-label mb-0">Month:</label>
                    <input type="month" name="month" value="{{ request('month') }}" class="form-control">
                </div>
                <div>
                    <label class="form-label mb-0">From:</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div>
                    <label class="form-label mb-0">To:</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div>
                    <label class="form-label mb-0">Year:</label>
                    <input type="number" name="year" value="{{ request('year') }}" class="form-control" min="2000" max="2100">
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route($prefix . 'dashboard') }}" class="btn btn-outline-danger btn-sm">Clear</a>
                </div>
            </form>


        </div>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="glass-card text-center kpi-box">
                    <div class="fw-bold">📦 Total Lost Items</div>
                    <h4>{{ $totalLost ?? 0 }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card text-center kpi-box">
                    <div class="fw-bold">🚫 Total Violations</div>
                    <h4>{{ $totalViolations ?? 0 }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card text-center kpi-box">
                    <div class="fw-bold">🔎 Most Lost Item</div>
                    <h4>{{ $topItem->item_type ?? 'N/A' }} ({{ $topItem->total ?? 0 }})</h4>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="glass-card text-center kpi-box">
                    <div class="fw-bold">✅ Claimed Items</div>
                    <h4>{{ $totalClaimed ?? 0 }}</h4>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card text-center kpi-box">
                    <div class="fw-bold">📍 Unclaimed Items</div>
                    <h4>{{ $totalUnclaimed ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="glass-card">
                    <h5 class="mb-3 fw-bold">📦 Lost Items Breakdown</h5>
                    <canvas id="lostFoundChart" height="200"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card">
                    <h5 class="mb-3 fw-bold">🚫 Violations Breakdown</h5>
                    <canvas id="violationChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Entries -->
        <div class="row">
            <div class="col-md-6">
                <div class="glass-card">
                    <h6 class="fw-bold">📝 Recent Lost & Found</h6>
                    @forelse($recent as $entry)
                    <div class="recent-card">
                        <div>
                            <a href="#" class="fw-bold viewLostItem"
                                data-bs-toggle="modal"
                                data-bs-target="#viewLostItemModal"
                                data-ticket="{{ $entry->ticket_no }}"
                                data-type="{{ $entry->item_type }}"
                                data-desc="{{ $entry->description }}"
                                data-reporter="{{ $entry->reporter_name }}"
                                data-status="{{ $entry->status }}"
                                data-date="{{ $entry->created_at->format('Y-m-d') }}"
                                data-location="{{ $entry->location_found ?? 'N/A' }}"
                                data-email="{{ $entry->email ?? 'N/A' }}">
                                {{ $entry->ticket_no }}
                            </a>
                            <div class="text-muted">{{ $entry->item_type }}</div>
                        </div>
                        <span class="glass-badge">{{ $entry->status }}</span>
                    </div>
                    @empty
                    <div class="text-muted">No recent lost items.</div>
                    @endforelse
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card">
                    <h6 class="fw-bold">📌 Recent Violations</h6>
                    @forelse($recentViolations as $v)
                    <div class="recent-card">
                        <div>
                            <a href="#" class="fw-bold">{{ $v->violation_no }}</a>
                            <div class="text-muted">{{ $v->offense }}</div>
                        </div>
                        <span class="glass-badge">{{ $v->status }}</span>
                    </div>
                    @empty
                    <div class="text-muted">No recent violations.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
<!-- View Lost Item Modal -->
<div class="modal fade" id="viewLostItemModal" tabindex="-1" aria-labelledby="viewLostItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="viewLostItemModalLabel">📄 Lost Item Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Ticket No</th>
                        <td id="modal-ticket"></td>
                    </tr>
                    <tr>
                        <th>Item Type</th>
                        <td id="modal-type"></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="modal-desc"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="modal-email"></td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td id="modal-location"></td>
                    </tr>
                    <tr>
                        <th>Reporter</th>
                        <td id="modal-reporter"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="modal-status"></td>
                    </tr>
                    <tr>
                        <th>Date Reported</th>
                        <td id="modal-date"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const viewLinks = document.querySelectorAll(".viewLostItem");

        viewLinks.forEach(link => {
            link.addEventListener("click", function() {
                document.getElementById("modal-ticket").textContent = this.dataset.ticket;
                document.getElementById("modal-type").textContent = this.dataset.type;
                document.getElementById("modal-desc").textContent = this.dataset.desc;
                document.getElementById("modal-reporter").textContent = this.dataset.reporter;
                document.getElementById("modal-status").textContent = this.dataset.status;
                document.getElementById("modal-date").textContent = this.dataset.date;
                document.getElementById("modal-location").textContent = this.dataset.location;
                document.getElementById("modal-email").textContent = this.dataset.email;
            });
        });
    });
</script>
<script>
    const labelsData = @json($labels);
    const countsData = @json($counts);

    const vLabelsData = @json($vLabels); // for violation
    const vCountsData = @json($vCounts);
</script>
<script src="{{ asset('js/lostfound.js') }}"></script>
@endpush