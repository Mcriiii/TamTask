@extends('layout.main')
@section('title', 'Dashboard')
@section('content')
<!-- Top Navbar -->
<div class="page-wrapper d-flex">
    @include('layout.sidebar')
    <div class="content-wrapper flex-grow-1 d-flex flex-column">
        <div class="top-navbar">
            <div class="mx-auto">
                <img src="{{ asset('images/name_logo.png') }}" alt="" class="logo-nav">
            </div>
            <div class="user-greeting">
                Hello, {{ Auth::user()->first_name }}
            </div>
        </div>
        <!-- 🌿 STYLES: Green BG + Glassmorphism -->
        <style>
            .glass-card {
                background-color: rgba(255, 255, 255, 0.81);
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

            /* design for filter and clear */
            .link-button {
                background: none;
                border: none;
                color: #006E44;
                /* FEU green */
                padding: 0;
                margin: 0;
                text-decoration: underline;
                cursor: pointer;
                font-weight: 500;
            }

            .link-button:hover {
                color: #004d3f;
                /* Slightly darker green */
                text-decoration: none;
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
                /* Adds space between incident cards */
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                /* Adds space between ticket number, description, and status badge */
                flex-wrap: wrap;
                /* Ensures content wraps on smaller screens */
            }

            .row {
                margin-bottom: 20px;
                /* Adds space between rows of cards */
            }

            a {
                color: #0077cc;
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            a.btn-secondary:hover {
                text-decoration: none !important;

            }

            .sidebar a:hover {
                text-decoration: none;
            }
        </style>

        <div class="main-wrapper">

            <div class="main-content">


                <!-- Header & Filter -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Dashboard</h2>
                    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <div>
                            <label for="month" class="form-label mb-0">Month:</label>
                            <input type="month" id="month" name="month" value="{{ request('month') }}" class="form-control">
                        </div>
                        <div>
                            <label for="from" class="form-label mb-0">From:</label>
                            <input type="date" id="from" name="from" value="{{ request('from') }}" class="form-control">
                        </div>
                        <div>
                            <label for="to" class="form-label mb-0">To:</label>
                            <input type="date" id="to" name="to" value="{{ request('to') }}" class="form-control">
                        </div>
                        <div>
                            <label for="year" class="form-label mb-0">Year:</label>
                            <input type="number" id="year" name="year" value="{{ request('year') }}" class="form-control" min="2000" max="2100">
                        </div>
                        <div class="mt-4 d-flex gap-3">
                            <button type="submit" class="link-button">Filter</button>
                            <a href="{{ route('dashboard') }}" class="link-button">Clear</a>
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
                    <div class="col-md-4">
                        <div class="glass-card text-center kpi-box">
                            <div class="fw-bold">✅ Claimed Items</div>
                            <h4>{{ $totalClaimed ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card text-center kpi-box">
                            <div class="fw-bold">📍 Unclaimed Items</div>
                            <h4>{{ $totalUnclaimed ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card text-center kpi-box">
                            <div class="fw-bold">⚠️ Most Violation</div>
                            @if ($topViolation)
                            <h4>{{ $topViolation->offense }} ({{ $topViolation->total }})</h4>
                            @else
                            <h4>None (0)</h4>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="glass-card">
                            <h5 class="mb-3 fw-bold">📦 Lost Items Breakdown</h5>
                            <canvas id="lostFoundChart" height="60"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Violations and Incidents in 2-column layout -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="glass-card">
                            <h5 class="mb-3 fw-bold">🚫 Violations Breakdown</h5>
                            <canvas id="violationChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card">
                            <h5 class="mb-3 fw-bold">⚠️ Incidents Breakdown</h5>
                            <canvas id="incidentChart" height="200"></canvas>
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
                                    <a href="#" class="fw-bold viewViolation"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewViolationModal"
                                        data-violation="{{ $v->violation_no }}"
                                        data-name="{{ $v->full_name }}"
                                        data-student="{{ $v->student_no }}"
                                        data-email="{{ $v->student_email }}"
                                        data-date="{{ $v->date_reported }}"
                                        data-degree="{{ $v->yearlvl_degree }}"
                                        data-offense="{{ $v->offense }}"
                                        data-level="{{ $v->level }}"
                                        data-status="{{ $v->status }}"
                                        data-action="{{ $v->action_taken ?? 'N/A' }}">
                                        {{ $v->violation_no }}
                                    </a>

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
                <div class="row">
                    <div class="col-md-6">
                        <div class="glass-card">
                            <h6 class="fw-bold">📌 Recent Incidents</h6>
                            @forelse($recentIncidents as $incident)
                            <div class="recent-card">
                                <div>
                                    <a href="#" class="fw-bold viewIncident"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewIncidentModal"
                                        data-ticket="{{ $incident->ticket_no }}"
                                        data-incident="{{ $incident->incident }}"
                                        data-reporter="{{ $incident->reporter_name }}"
                                        data-status="{{ $incident->status }}"
                                        data-date="{{ \Carbon\Carbon::parse($incident->date_reported)->format('Y-m-d') }}">
                                        {{ $incident->ticket_no }}
                                    </a>
                                    <div class="text-muted">{{ $incident->incident }}</div>
                                </div>
                                <span class="glass-badge" style="background-color: {{ $incident->level_color }}">{{ $incident->level }}</span>
                            </div>
                            @empty
                            <div class="text-muted">No recent incidents.</div>
                            @endforelse
                        </div>
                    </div>
                    <!-- Priority Incidents Column -->
                    <div class="col-md-6">
                        <div class="glass-card">
                            <h6 class="fw-bold">🚨 Priority Incidents</h6>
                            @forelse($priorityIncidents as $incident)
                            @if($incident->status !== 'Completed') <!-- Only show if not "Completed" -->
                            <div class="recent-card">
                                <div>
                                    <a href="#" class="fw-bold viewIncident"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewIncidentModal"
                                        data-ticket="{{ $incident->ticket_no }}"
                                        data-incident="{{ $incident->incident }}"
                                        data-reporter="{{ $incident->reporter_name }}"
                                        data-status="{{ $incident->status }}"
                                        data-date="{{ \Carbon\Carbon::parse($incident->date_reported)->format('Y-m-d') }}">
                                        {{ $incident->ticket_no }}
                                    </a>
                                    <div class="text-muted">{{ $incident->incident }}</div>
                                </div>
                                <span class="glass-badge" style="background-color: {{ $incident->level_color }}">{{ $incident->level }}</span>
                            </div>
                            @endif
                            @empty
                            <div class="text-muted">No priority incidents.</div>
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

        <!-- View Violation Modal -->
        <div class="modal fade" id="viewViolationModal" tabindex="-1" aria-labelledby="viewViolationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="viewViolationModalLabel">🚫 Violation Details 🚫</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Violation No</th>
                                <td id="vmodal-violation"></td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td id="vmodal-name"></td>
                            </tr>
                            <tr>
                                <th>Student No</th>
                                <td id="vmodal-student"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td id="vmodal-email"></td>
                            </tr>
                            <tr>
                                <th>Year Level & Degree</th>
                                <td id="vmodal-degree"></td>
                            </tr>
                            <tr>
                                <th>Offense</th>
                                <td id="vmodal-offense"></td>
                            </tr>
                            <tr>
                                <th>Level</th>
                                <td id="vmodal-level"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="vmodal-status"></td>
                            </tr>
                            <tr>
                                <th>Action Taken</th>
                                <td id="vmodal-action"></td>
                            </tr>
                            <tr>
                                <th>Date Reported</th>
                                <td id="vmodal-date"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Incident Modal -->
        <div class="modal fade" id="viewIncidentModal" tabindex="-1" aria-labelledby="viewIncidentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="viewIncidentModalLabel">📄 Incident Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Ticket No</th>
                                <td id="incident-ticket"></td>
                            </tr>
                            <tr>
                                <th>Incident</th>
                                <td id="incident-desc"></td>
                            </tr>
                            <tr>
                                <th>Reporter</th>
                                <td id="incident-reporter"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="incident-status"></td>
                            </tr>
                            <tr>
                                <th>Date Reported</th>
                                <td id="incident-date"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const viewLinks = document.querySelectorAll(".viewLostItem");
        const viewViolationLinks = document.querySelectorAll(".viewViolation");

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

        viewViolationLinks.forEach(link => {
            link.addEventListener("click", function() {
                document.getElementById("vmodal-violation").textContent = this.dataset.violation;
                document.getElementById("vmodal-name").textContent = this.dataset.name;
                document.getElementById("vmodal-student").textContent = this.dataset.student;
                document.getElementById("vmodal-email").textContent = this.dataset.email;
                document.getElementById("vmodal-degree").textContent = this.dataset.degree;
                document.getElementById("vmodal-offense").textContent = this.dataset.offense;
                document.getElementById("vmodal-level").textContent = this.dataset.level;
                document.getElementById("vmodal-status").textContent = this.dataset.status;
                document.getElementById("vmodal-action").textContent = this.dataset.action;
                document.getElementById("vmodal-date").textContent = this.dataset.date;
            });
        });

        const viewIncidentLinks = document.querySelectorAll(".viewIncident");

        viewIncidentLinks.forEach(link => {
            link.addEventListener("click", function() {
                document.getElementById("incident-ticket").textContent = this.dataset.ticket;
                document.getElementById("incident-desc").textContent = this.dataset.incident;
                document.getElementById("incident-reporter").textContent = this.dataset.reporter;
                document.getElementById("incident-status").textContent = this.dataset.status;
                document.getElementById("incident-date").textContent = this.dataset.date;
            });
        });
    });
</script>
<script>
    const labelsData = @json($labels);
    const countsData = @json($counts);

    const vLabelsData = @json($vLabels); // for violation
    const vCountsData = @json($vCounts);

    const iLabelsData = @json($iLabels);
    const iCountsData = @json($iCounts);
</script>
<script src="{{ asset('js/lostfound.js') }}"></script>
@endpush