@props(['stats'])


<div class="card shadow-sm mt-4 border-0">
  <div class="card-header text-white rounded-top" style="background-color: #38b000;">
    <h5 class="mb-0">Student Violation Summary</h5>
  </div>
  <div class="card-body p-0">
    @if($stats->isEmpty())
      <p class="p-4 text-center text-muted">No student violations yet.</p>
    @else
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-success">
            <tr class="text-center">
              <th>Student No</th>
              <th>Name</th>
              <th>Total</th>
              <th>Minors</th>
              <th>Majors</th>
            </tr>
          </thead>
          <tbody>
            @foreach($stats as $s)
              <tr class="text-center">
                <td class="fw-semibold">{{ $s->student_no }}</td>
                <td>{{ $s->full_name }}</td>
                <td>
                  <span class="badge bg-dark px-3">{{ $s->total_minors + $s->total_majors }}</span>
                </td>
                <td>
                  <span class="badge bg-warning text-dark">{{ $s->total_minors }}</span>
                </td>
                <td>
                  <span class="badge bg-danger">{{ $s->total_majors }}</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
