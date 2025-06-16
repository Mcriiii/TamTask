@php
// Only show if not yet resolved
@endphp
@if($violation->status !== 'Complete')
<form method="POST" action="{{ route($prefix . 'violations.take-action', $violation->id) }}" class="d-flex align-items-center">
    @csrf @method('PUT')
    <select name="action_taken" class="form-select form-select-sm me-1" required>
        <option value="" disabled selected>Select action</option>
        <optgroup label="General">
            <option value="Warning">Verbal/Written Warning</option>
            <option value="Parent/Guardian Conference">Parent/Guardian Conference</option>
        </optgroup>
        <optgroup label="Sanctions">
            <option value="Suspension">Suspension</option>
            <option value="Disciplinary Probation">Disciplinary Probation</option>
            <option value="DUSAP">DUSAP</option>
            <option value="Community Service">Community Service</option>
            @if($violation->level === 'Major')
                <option value="Expulsion">Expulsion</option>
            @endif
        </optgroup>
    </select>
    <button type="submit" class="btn btn-sm btn-success">Resolve</button>
</form>
@else
    <span class="badge bg-secondary">{{ $violation->action_taken ?? 'Resolved' }}</span>
@endif
