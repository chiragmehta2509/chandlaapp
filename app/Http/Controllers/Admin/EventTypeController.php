<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventTypeController extends Controller
{
    public function index()
    {
        $eventTypes = EventType::ordered()->paginate(20);
        return view('admin.event-types.index', compact('eventTypes'));
    }

    public function create()
    {
        return view('admin.event-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        EventType::create($validated);

        return redirect()->route('admin.event-types.index')->with('success', 'Event type created successfully');
    }

    public function edit($id)
    {
        $eventType = EventType::findOrFail($id);
        return view('admin.event-types.edit', compact('eventType'));
    }

    public function update(Request $request, $id)
    {
        $eventType = EventType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $eventType->update($validated);

        return redirect()->route('admin.event-types.index')->with('success', 'Event type updated successfully');
    }

    public function destroy($id)
    {
        $eventType = EventType::findOrFail($id);
        
        // Check if any events are using this type
        if ($eventType->events()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete event type that is being used by events']);
        }

        $eventType->delete();

        return redirect()->route('admin.event-types.index')->with('success', 'Event type deleted successfully');
    }
}
