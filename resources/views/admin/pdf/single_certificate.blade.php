<!DOCTYPE html>
<html>
<head>
    <title>Certificate Request</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 30px; }
        h2 { text-align: center; margin-bottom: 30px; }
        p { margin: 8px 0; }
    </style>
</head>
<body>
    <h2>Certificate Request</h2>

    <p><strong>Ticket No:</strong> {{ $certificate->ticket_no }}</p>
    <p><strong>Name:</strong> {{ $certificate->requester_name }}</p>
    <p><strong>Email:</strong> {{ $certificate->email }}</p>
    <p><strong>Student No:</strong> {{ $certificate->student_no }}</p>
    <p><strong>Year & Degree:</strong> {{ $certificate->yearlvl_degree }}</p>
    <p><strong>Date Requested:</strong> {{ $certificate->date_requested }}</p>
    <p><strong>Purpose:</strong> {{ $certificate->purpose }}</p>
    <p><strong>Status:</strong> {{ $certificate->status }}</p>
</body>
</html>
