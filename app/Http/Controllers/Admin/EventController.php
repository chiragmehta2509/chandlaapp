<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('user');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            if ($request->status === 'archived') {
                $query->where('is_archived', true);
            } else {
                $query->where('is_archived', false);
            }
        }

        $events = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function show($id)
    {
        $event = Event::with(['user', 'entries', 'invitations', 'collaborators'])->findOrFail($id);
        return view('admin.events.show', compact('event'));
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully');
    }
}
