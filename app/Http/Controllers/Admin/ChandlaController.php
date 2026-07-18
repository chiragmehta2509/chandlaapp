<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use Illuminate\Http\Request;

class ChandlaController extends Controller
{
    public function index(Request $request)
    {
        $query = Chandla::with(['event', 'user']);

        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('received_date', [$request->start_date, $request->end_date]);
        }

        $chandlas = $query->orderBy('received_date', 'desc')->paginate(20);
        $events = Event::orderBy('event_date', 'desc')->get();

        $stats = [
            'total' => Chandla::count(),
            'total_amount' => Chandla::sum('amount'),
            'chandla_count' => Chandla::where('category', 'chandla')->count(),
            'cover_count' => Chandla::where('category', 'cover')->count(),
            'gift_count' => Chandla::where('category', 'gift')->count(),
        ];

        return view('admin.chandlas.index', compact('chandlas', 'events', 'stats'));
    }

    public function show($id)
    {
        $chandla = Chandla::with(['event', 'user'])->findOrFail($id);
        return view('admin.chandlas.show', compact('chandla'));
    }

    public function destroy($id)
    {
        $chandla = Chandla::findOrFail($id);
        $chandla->delete();

        return redirect()->route('admin.chandlas.index')->with('success', 'Chandla record deleted successfully');
    }

    public function verify($id)
    {
        $chandla = Chandla::findOrFail($id);
        $chandla->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Chandla verified successfully');
    }
}
