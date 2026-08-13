<?php

namespace App\Http\Controllers\Api\Ganpati;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use App\Models\EventCashInventory;
use App\Models\EventType;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GanpatiController extends Controller
{
    private function ganpatiEventTypeId(): ?int
    {
        static $id = null;
        if ($id === null) {
            $et = EventType::where('slug', 'ganpati_special')->first();
            $id = $et?->id;
        }
        return $id;
    }

    private function userGanpatiEvents(Request $request)
    {
        $userId = $request->user()->dataOwnerId();
        return Event::where('user_id', $userId)
            ->where('event_type_id', $this->ganpatiEventTypeId());
    }

    /**
     * Check if a Ganpati event already exists for the logged-in user.
     * GET /api/v1/ganpati/check-exists
     */
    public function checkExists(Request $request)
    {
        $event = $this->userGanpatiEvents($request)->first();

        if ($event) {
            return response()->json([
                'success' => true,
                'data' => [
                    'eventExists' => true,
                    'eventId' => $event->id,
                    'event' => [
                        'id' => $event->id,
                        'title' => $event->title,
                        'event_date' => $event->event_date instanceof \Carbon\Carbon ? $event->event_date->format('Y-m-d') : $event->event_date,
                        'venue' => $event->venue,
                        'upi_id' => $event->upi_id,
                        'status' => $event->status,
                        'created_at' => $event->created_at,
                    ]
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'eventExists' => false,
                'eventId' => null,
                'event' => null
            ]
        ]);
    }

    private function denominations(): array
    {
        return [1, 2, 5, 10, 20, 50, 100, 200, 500];
    }

    /**
     * List all Ganpati events for the authenticated user.
     * GET /api/v1/ganpati
     */
    public function index(Request $request)
    {
        $query = $this->userGanpatiEvents($request)->withCount('chandlas');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('venue', 'like', "%{$s}%");
            });
        }

        $events = $query->orderByDesc('event_date')->orderByDesc('id')->get()
            ->map(function ($event) {
                $data = $event->toArray();
                // Ensure upi_id is always present (null if not set)
                $data['upi_id']           = $event->upi_id;
                // Provide full public URL for the scanner QR image
                $data['scanner_image_url'] = $event->gpay_qr_image
                    ? Storage::disk('public')->url($event->gpay_qr_image)
                    : null;
                return $data;
            });

        return response()->json([
            'success' => true,
            'data'    => $events,
        ]);
    }

    /**
     * Create a new Ganpati event.
     * POST /api/v1/ganpati
     */
    public function store(Request $request)
    {
        if ($this->userGanpatiEvents($request)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'You can only create one Ganpati Special event. Please use the regular Events module for other events.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'event_date'  => 'required|date',
            'venue'       => 'nullable|string|max:255',
            'upi_id'      => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();

        $event = Event::create([
            'user_id'       => $userId,
            'event_type_id' => $this->ganpatiEventTypeId(),
            'title'         => $request->input('title'),
            'event_date'    => $request->input('event_date'),
            'venue'         => $request->input('venue'),
            'upi_id'        => $request->input('upi_id'),
            'description'   => $request->input('description'),
            'status'        => 'active',
            'pricing_plan'  => 'unlimited',
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_ganpati_event',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'new_values' => $event->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ganpati event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * View details, stats, and chanda entries of a Ganpati event.
     * GET /api/v1/ganpati/{id}
     */
    public function show(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);

        $entries = Chandla::where('event_id', $event->id)
            ->orderBy('received_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalAmount  = (float) $entries->sum('amount');
        $cashAmount   = (float) $entries->where('payment_method', 'cash')->sum('amount');
        $gpayAmount   = (float) $entries->where('payment_method', 'gpay')->sum('amount');
        $otherAmount  = (float) $entries->whereNotIn('payment_method', ['cash', 'gpay'])->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'event' => $event,
                'stats' => [
                    'total_amount'  => $totalAmount,
                    'cash_amount'   => $cashAmount,
                    'gpay_amount'   => $gpayAmount,
                    'other_amount'  => $otherAmount,
                    'total_entries' => $entries->count(),
                ],
                'entries' => $entries
            ]
        ]);
    }

    /**
     * Update a Ganpati event.
     * PUT /api/v1/ganpati/{id}
     */
    public function update(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|required|string|max:255',
            'event_date'  => 'sometimes|required|date',
            'venue'       => 'nullable|string|max:255',
            'upi_id'      => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update($request->only([
            'title', 'event_date', 'venue', 'upi_id', 'description'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Ganpati event updated successfully',
            'data' => $event
        ]);
    }

    /**
     * Delete a Ganpati event and its entries.
     * DELETE /api/v1/ganpati/{id}
     */
    public function destroy(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);

        DB::transaction(function () use ($event) {
            Chandla::where('event_id', $event->id)->delete();
            $event->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Ganpati event and associated entries deleted successfully'
        ]);
    }

    /**
     * Update scanner details (UPI ID and/or scanner QR image).
     * POST /api/v1/ganpati/{id}/scanner
     */
    public function updateScanner(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'upi_id'     => 'nullable|string|max:255',
            'scanner_qr' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('scanner_qr')) {
            if ($event->gpay_qr_image) {
                Storage::disk('public')->delete($event->gpay_qr_image);
            }
            $event->gpay_qr_image = $request->file('scanner_qr')->store('ganpati/scanners', 'public');
        }

        if ($request->has('upi_id')) {
            $event->upi_id = $request->input('upi_id');
        }

        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Scanner details updated successfully',
            'data' => $event
        ]);
    }

    /**
     * Create a new chanda entry for a Ganpati event.
     * POST /api/v1/ganpati/{id}/chandlas
     */
    public function storeChandla(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'giver_name'           => 'required|string|max:255',
            'giver_phone'          => 'nullable|string|max:30',
            'giver_address'        => 'nullable|string',
            'amount'               => 'required|numeric|min:0',
            'payment_method'       => 'required|string|in:cash,gpay,other',
            'gpay_transaction_id'  => 'nullable|string|max:255',
            'gpay_image'           => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'received_date'        => 'required|date',
            'notes'                => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();
        $gpayImagePath = null;

        if ($request->hasFile('gpay_image')) {
            $gpayImagePath = $request->file('gpay_image')->store('ganpati/gpay_proofs', 'public');
        }

        $chandla = Chandla::create([
            'user_id'             => $userId,
            'event_id'            => $event->id,
            'category'            => 'chandla',
            'giver_name'          => $request->input('giver_name'),
            'giver_phone'         => $request->input('giver_phone'),
            'giver_address'       => $request->input('giver_address'),
            'amount'              => $request->input('amount'),
            'payment_method'      => $request->input('payment_method'),
            'gpay_transaction_id' => $request->input('gpay_transaction_id'),
            'gpay_image'          => $gpayImagePath,
            'received_date'       => $request->input('received_date'),
            'notes'               => $request->input('notes'),
        ]);

        // Process cash notes breakdown if payment is cash
        if ($request->input('payment_method') === 'cash') {
            $notesCount = [];
            foreach ($this->denominations() as $d) {
                $count = (int) $request->input("cash_note_{$d}", 0);
                if ($count > 0) {
                    $notesCount[$d] = $count;
                }
            }

            if (!empty($notesCount)) {
                $defaults = [];
                foreach ($this->denominations() as $d) {
                    $defaults["note_{$d}"] = 0;
                }
                $inv = EventCashInventory::firstOrCreate(
                    ['event_id' => $event->id],
                    $defaults
                );
                foreach ($notesCount as $denom => $cnt) {
                    $column = "note_{$denom}";
                    $inv->{$column} += $cnt;
                }
                $inv->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Chanda entry added successfully',
            'data' => $chandla
        ], 201);
    }

    /**
     * Update an existing chanda entry.
     * PUT /api/v1/ganpati/{id}/chandlas/{chandlaId}
     */
    public function updateChandla(Request $request, $id, $chandlaId)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);
        $chandla = Chandla::where('event_id', $event->id)->findOrFail($chandlaId);

        $validator = Validator::make($request->all(), [
            'giver_name'          => 'sometimes|required|string|max:255',
            'giver_phone'         => 'nullable|string|max:30',
            'giver_address'       => 'nullable|string',
            'amount'              => 'sometimes|required|numeric|min:0',
            'payment_method'      => 'sometimes|required|string|in:cash,gpay,other',
            'gpay_transaction_id' => 'nullable|string|max:255',
            'gpay_image'          => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'received_date'       => 'sometimes|required|date',
            'notes'               => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('gpay_image')) {
            if ($chandla->gpay_image) {
                Storage::disk('public')->delete($chandla->gpay_image);
            }
            $chandla->gpay_image = $request->file('gpay_image')->store('ganpati/gpay_proofs', 'public');
        }

        $chandla->update($request->only([
            'giver_name', 'giver_phone', 'giver_address', 'amount',
            'payment_method', 'gpay_transaction_id', 'received_date', 'notes'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Chanda entry updated successfully',
            'data' => $chandla
        ]);
    }

    /**
     * Delete a chanda entry.
     * DELETE /api/v1/ganpati/{id}/chandlas/{chandlaId}
     */
    public function destroyChandla(Request $request, $id, $chandlaId)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);
        $chandla = Chandla::where('event_id', $event->id)->findOrFail($chandlaId);

        if ($chandla->gpay_image) {
            Storage::disk('public')->delete($chandla->gpay_image);
        }

        $chandla->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chanda entry deleted successfully'
        ]);
    }

    /**
     * List all chanda entries for a Ganpati event.
     * GET /api/v1/ganpati/{id}/chandlas
     */
    public function listChandlas(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);

        $entries = Chandla::where('event_id', $event->id)
            ->orderBy('received_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $entries,
            'count'   => $entries->count(),
        ]);
    }

    /**
     * Show a single chanda entry.
     * GET /api/v1/ganpati/{id}/chandlas/{chandlaId}
     */
    public function showChandla(Request $request, $id, $chandlaId)
    {
        $event   = $this->userGanpatiEvents($request)->findOrFail($id);
        $chandla = Chandla::where('event_id', $event->id)->findOrFail($chandlaId);

        return response()->json([
            'success' => true,
            'data'    => $chandla,
        ]);
    }

    /**
     * Return UPI ID and QR code (SVG + base64) for the event.
     * GET /api/v1/ganpati/{id}/qr
     */
    public function qr(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)->findOrFail($id);

        if (!$event->upi_id) {
            return response()->json([
                'success' => false,
                'message' => 'No UPI ID configured for this event.',
            ], 404);
        }

        $upiUrl = 'upi://pay?pa=' . urlencode($event->upi_id)
            . '&pn=' . urlencode($event->title)
            . '&cu=INR';

        $svg       = QrCode::size(400)->generate($upiUrl);
        $svgString = (string) $svg;
        $base64    = 'data:image/svg+xml;base64,' . base64_encode($svgString);

        // Scanner QR image uploaded manually (if any)
        $scannerImageUrl = $event->gpay_qr_image
            ? Storage::disk('public')->url($event->gpay_qr_image)
            : null;

        return response()->json([
            'success' => true,
            'data'    => [
                'upi_id'            => $event->upi_id,
                'upi_url'           => $upiUrl,
                'qr_svg'            => $svgString,
                'qr_base64'         => $base64,
                'scanner_image_url' => $scannerImageUrl,
            ],
        ]);
    }

    /**
     * Download or stream PDF for a Ganpati event.
     * GET /api/v1/ganpati/{id}/pdf
     */
    public function downloadPdf(Request $request, $id)
    {
        $event = $this->userGanpatiEvents($request)
            ->with('chandlas')
            ->findOrFail($id);

        $entries = $event->chandlas
            ->sortBy(fn($r) => mb_strtolower(trim((string) $r->giver_name)))
            ->values();

        $cash  = $entries->where('payment_method', 'cash');
        $gpay  = $entries->where('payment_method', 'gpay');
        $other = $entries->whereNotIn('payment_method', ['cash', 'gpay']);

        $pdf = Pdf::loadView('client.ganpati.pdf', compact('event', 'entries', 'cash', 'gpay', 'other'))
            ->setPaper('a4', 'portrait');

        try {
            $dompdf      = $pdf->getDomPDF();
            $canvas      = $dompdf->getCanvas();
            $fontMetrics = $dompdf->getFontMetrics();
            $font        = $fontMetrics->get_font('DejaVu Sans', 'normal');
            if ($font) {
                $muted = [0.38, 0.41, 0.45];
                $canvas->page_text(34, 806, 'Chandla Book · Ganpati Special', $font, 7.5, $muted);
                $canvas->page_text(238, 806, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 9, [0.18, 0.23, 0.29]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Ganpati PDF footer skipped: ' . $e->getMessage());
        }

        $filename = 'ganpati-chanda-' . $event->id . '.pdf';

        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
