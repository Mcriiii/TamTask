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
    
    <th>Last Action</th>
    <th>Resolve</th>
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
      
      <td>
        @if($s->last_action)
          <span class="badge bg-info text-white">{{ $s->last_action }}</span>
        @else
          &mdash;
        @endif
      </td>
      <td>
        @if($s->escalated)
          <form method="POST" action="{{ route('admin.violations.resolve', $s->student_no) }}" class="d-flex align-items-center">
            @csrf
            <select name="action_taken" class="form-select form-select-sm me-2" required>
              <option value="">Select action...</option>
              @foreach(['Warning', 'Parent/Guardian Conference', 'Suspension', 'Disciplinary Probation', 'DUSAP', 'Community Service', 'Expulsion'] as $act)
                <option value="{{ $act }}">{{ $act }}</option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-success">Resolve</button>

            {{-- Hidden fields --}}
            <input type="hidden" name="full_name" value="{{ $s->full_name }}">
            <input type="hidden" name="student_email" value="{{ $s->student_email }}">
            <input type="hidden" name="yearlvl_degree" value="{{ $s->yearlvl_degree }}">
          </form>
        @else
          &mdash;
        @endif
      </td>
    </tr>
  @endforeach
</tbody>
      </table>
    @endif
  </div>
</div>
