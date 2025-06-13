<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lost and Found Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-transform: uppercase;
        }

        h2 {
            text-align: center;
        }

        .summary {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Lost and Found Reports</h2>

    <div class="summary">
        <p><strong>Total Reports:</strong> {{ $total }}</p>
        <p><strong>Claimed:</strong> {{ $claimed }}</p>
        <p><strong>Unclaimed:</strong> {{ $unclaimed }}</p>
        @if($topItem)
        <p><strong>Most Lost Item:</strong> {{ $topItem['item'] }} ({{ $topItem['count'] }} reports)</p>
        @else
        <p><strong>Most Lost Item:</strong> No most lost item (all types equal)</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket No</th>
                <th>Reporter</th>
                <th>Date</th>
                <th>Location</th>
                <th>Item</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $r)
            <tr>
                <td>{{ $r->ticket_no }}</td>
                <td>{{ $r->reporter_name }}</td>
                <td>{{ \Carbon\Carbon::parse($r->date_reported)->format('M d, Y') }}</td>
                <td>{{ $r->location_found ?? 'N/A' }}</td>
                <td>{{ $r->item_type }}</td>
                <td>{{ $r->description }}</td>
                <td>{{ $r->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>