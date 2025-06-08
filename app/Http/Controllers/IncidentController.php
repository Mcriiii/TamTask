<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reporter_name', 'like', "%{$search}%")
                    ->orWhere('ticket_no', 'like', "%{$search}%")
                    ->orWhere('incident', 'like', "%{$search}%");
            });
        }

        if ($request->filled('reporter_role')) {
            $query->where('reporter_role', $request->reporter_role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_reported')) {
            $query->whereDate('date_reported', $request->date_reported);
        }

        $incidents = $query->latest()->paginate(10);
       return view('incident', compact('incidents'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_no' => 'required|unique:incidents,ticket_no',
            'incident' => 'required|string|max:1000',
            'reporter_name' => 'required|string|max:255',
            'date_reported' => 'required|date',
            'reporter_role' => 'required|string|max:100',
            'status' => 'required|in:Pending,Complete',
        ]);

        $data = $request->all();
        $data['level'] = (new Incident($data))->level; // auto-compute level
        Incident::create($data);

        return redirect()->route('incidents.index')->with('success', 'Incident report added.');
    }

    public function edit($id)
    {
        $incident = Incident::findOrFail($id);
        return response()->json([
            'html' => view('partials.incident_edit_form', compact('incident'))->render()
        ]);
    }

    public function update(Request $request, $id)
    {
        $incident = Incident::findOrFail($id);

        $request->validate([
            'incident' => 'required|string|max:1000',
            'reporter_name' => 'required|string',
            'date_reported' => 'required|date',
            'reporter_role' => 'required|string',
            'status' => 'required|in:Pending,Complete',
        ]);

        $data = $request->all();
        $data['level'] = (new Incident($data))->level;
        $incident->update($data);

        return redirect()->route('incidents.index')->with('success', 'Incident updated.');
    }

    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);
        $incident->delete();
        return redirect()->route('incidents.index')->with('success', 'Incident deleted.');
    }
}
