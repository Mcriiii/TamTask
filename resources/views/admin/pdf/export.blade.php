<!DOCTYPE html>
<html>
<head>
    <title>Lost and Found Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>Lost and Found Report</h2>

    @if($month || $itemType || $location || $reporter)
        <p><strong>Filters:</strong></p>
        <ul>
            @if($month)<li>Month: {{ \Carbon\Carbon::parse($month)->format('F Y') }}</li>@endif
            @if($itemType)<li>Item Type: {{ $itemType }}</li>@endif
            @if($location)<li>Location: {{ $location }}</li>@endif
            @if($reporter)<li>Reporter: {{ $reporter }}</li>@endif
        </ul>
    @endif

    <h3>📊 Most Reported Items</h3>
    <table>
        <thead>
            <tr>
                <th>Item Type</th>
                <th>Total Reports</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->item_type }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>📌 Status Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusSummary as $status)
                <tr>
                    <td>{{ $status->status }}</td>
                    <td>{{ $status->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>📝 All Matching Entries</h3>
    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Reporter</th>
                <th>Item</th>
                <th>Status</th>
                <th>Location</th>
                <th>Date Reported</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                <tr>
                    <td>{{ $entry->ticket_no }}</td>
                    <td>{{ $entry->reporter_name }}</td>
                    <td>{{ $entry->item_type }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>{{ $entry->location_found }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry->date_reported)->format('F d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
