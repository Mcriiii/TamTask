@props(['stats'])

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card mt-4">
  <div class="card-header">
    <h5>Student Violation Summary</h5>
  </div>
  <div class="card-body p-0">
    @if($stats->isEmpty())
      <p class="p-3">No student violations yet.</p>
    @else
      <table class="table mb-0">
        <thead>
          <tr>
            <th>Student No</th>
            <th>Name</th>
            <th>Total Violations</th>
            <th>Minors</th>
            <th>Majors</th>
          </tr>
        </thead>
        <tbody>
          @foreach($stats as $s)
            <tr>
              <td>{{ $s->student_no }}</td>
              <td>{{ $s->full_name }}</td>
              <td>{{ $s->total_minors + $s->total_majors }}</td>
              <td>{{ $s->total_minors }}</td>
              <td>{{ $s->total_majors }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
