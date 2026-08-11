<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use App\Models\EventCashInventory;
use App\Models\EventType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * GanpatiController
 *
 * Handles the "Ganpati Special" module — completely separate from the regular events/chandlas
 * flow. All users get unlimited entries and PDF downloads for FREE (no plan checks).
 */
class GanpatiController extends Controller
{
    // ── Helpers ──────────────────────────────────────────────────────────────

    private function ganpatiEventTypeId(): ?int
    {
        static $id = null;
        if ($id === null) {
            $et = EventType::where('slug', 'ganpati_special')->first();
            $id = $et?->id;
        }
        return $id;
    }

    /** Scope: events owned by this user that are of type ganpati_special */
    private function userGanpatiEvents()
    {
        return Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->where('event_type_id', $this->ganpatiEventTypeId());
    }

    private function denominations(): array
    {
        return [500, 200, 100, 50, 20, 10, 5, 2, 1];
    }

    private function emptyNotes(): array
    {
        $notes = [];
        foreach ($this->denominations() as $d) {
            $notes[$d] = 0;
        }
        return $notes;
    }

    private function extractNotes(array $data, string $prefix): array
    {
        $notes = [];
        foreach ($this->denominations() as $d) {
            $notes[$d] = (int) ($data[$prefix . $d] ?? 0);
        }
        return $notes;
    }

    private function sumNotes(array $notes): int
    {
        $total = 0;
        foreach ($notes as $d => $count) {
            $total += $d * $count;
        }
        return $total;
    }

    private function isWholeNumber($value): bool
    {
        return is_numeric($value) && floor((float) $value) == (float) $value;
    }

    private function addNotes(array $base, array $delta): array
    {
        foreach ($this->denominations() as $d) {
            $base[$d] = ($base[$d] ?? 0) + ($delta[$d] ?? 0);
        }
        return $base;
    }

    private function subtractNotes(array $base, array $delta): array
    {
        foreach ($this->denominations() as $d) {
            $base[$d] = ($base[$d] ?? 0) - ($delta[$d] ?? 0);
        }
        return $base;
    }

    private function hasNegativeNotes(array $notes): bool
    {
        foreach ($notes as $count) {
            if ($count < 0) return true;
        }
        return false;
    }

    private function calculateChangeNotes(int $changeAmount, array $availableNotes): array
    {
        $remaining = $changeAmount;
        $notes = $this->emptyNotes();
        foreach ($this->denominations() as $d) {
            if ($remaining <= 0) break;
            $available = $availableNotes[$d] ?? 0;
            if ($available <= 0) continue;
            $need = intdiv($remaining, $d);
            if ($need <= 0) continue;
            $use = min($need, $available);
            $notes[$d] = $use;
            $remaining -= $use * $d;
        }
        return ['requested' => $changeAmount, 'remaining' => $remaining, 'notes' => $notes];
    }

    private function getInventoryForUpdate(int $eventId): EventCashInventory
    {
        $defaults = array_combine(
            array_map(fn($d) => 'note_' . $d, $this->denominations()),
            array_fill(0, count($this->denominations()), 0)
        );
        EventCashInventory::firstOrCreate(['event_id' => $eventId], $defaults);
        return EventCashInventory::where('event_id', $eventId)->lockForUpdate()->first();
    }

    private function getInventoryEffect(Chandla $chandla): array
    {
        $isCash = $chandla->category === 'chandla' && $chandla->payment_method === 'cash';
        if (!$isCash) {
            return ['is_cash' => false, 'received' => $this->emptyNotes(), 'change' => $this->emptyNotes()];
        }
        $received = [];
        $change = [];
        foreach ($this->denominations() as $d) {
            $received[$d] = (int) $chandla->{'cash_note_' . $d};
            $change[$d]   = (int) $chandla->{'change_note_' . $d};
        }
        return ['is_cash' => true, 'received' => $received, 'change' => $change];
    }

    // ── Event CRUD ────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $events = $this->userGanpatiEvents()->get();

        if ($events->isEmpty()) {
            return redirect()->route('client.ganpati.create');
        }

        return redirect()->route('client.ganpati.show', $events->first()->id);
    }

    public function create()
    {
        if ($this->userGanpatiEvents()->count() > 0) {
            return redirect()->route('client.ganpati.index')
                ->with('error', 'You can only create one Ganpati Special event. Please use the regular Events module for other events.');
        }
        return view('client.ganpati.create');
    }

    public function store(Request $request)
    {
        if ($this->userGanpatiEvents()->count() > 0) {
            return redirect()->route('client.ganpati.index')
                ->with('error', 'You can only create one Ganpati Special event. Please use the regular Events module for other events.');
        }

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'venue'      => 'nullable|string|max:255',
            'upi_id'     => 'nullable|string|max:255',
            'description'=> 'nullable|string',
        ]);

        $typeId = $this->ganpatiEventTypeId();
        if (!$typeId) {
            return back()->withErrors(['type' => 'Ganpati event type not configured. Please run the seeder.'])->withInput();
        }

        $ownerId = Auth::user()->dataOwnerId();

        $event = Event::create([
            'user_id'        => $ownerId,
            'title'          => $validated['title'],
            'event_date'     => $validated['event_date'],
            'venue'          => $validated['venue'] ?? null,
            'upi_id'         => $validated['upi_id'] ?? null,
            'description'    => $validated['description'] ?? null,
            'event_type_id'  => $typeId,
            'pricing_plan'   => 'unlimited',   // always unlimited / free
            'free_entry_limit' => 9999,
        ]);

        return redirect()->route('client.ganpati.chandla.create', $event->id)
            ->with('success', 'Ganpati event created! You can now start adding chanda entries.');
    }

    public function show($id)
    {
        $event = $this->userGanpatiEvents()
            ->with(['chandlas' => fn($q) => $q->orderBy('received_date', 'desc')->orderBy('id', 'desc')])
            ->findOrFail($id);

        $totalAmount  = $event->chandlas->where('category', 'chandla')->sum('amount');
        $cashAmount   = $event->chandlas->where('category', 'chandla')->where('payment_method', 'cash')->sum('amount');
        $gpayAmount   = $event->chandlas->where('category', 'chandla')->where('payment_method', 'gpay')->sum('amount');
        $otherAmount  = $event->chandlas->where('category', 'chandla')
            ->whereNotIn('payment_method', ['cash', 'gpay'])->sum('amount');
        $totalEntries = $event->chandlas->count();

        return view('client.ganpati.show', compact(
            'event', 'totalAmount', 'cashAmount', 'gpayAmount', 'otherAmount', 'totalEntries'
        ));
    }

    public function edit($id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);
        return view('client.ganpati.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'venue'      => 'nullable|string|max:255',
            'upi_id'     => 'nullable|string|max:255',
            'description'=> 'nullable|string',
        ]);

        $event->update($validated);

        return redirect()->route('client.ganpati.show', $event->id)
            ->with('success', 'Event updated successfully.');
    }

    public function destroy($id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);
        // Delete related chandlas
        $event->chandlas()->delete();
        $event->delete();

        return redirect()->route('client.ganpati.index')
            ->with('success', 'Ganpati event deleted.');
    }

    // ── Scanner (UPI QR) ─────────────────────────────────────────────────────

    public function scanner($id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);
        return view('client.ganpati.scanner', compact('event'));
    }

    public function scannerSave(Request $request, $id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);

        $request->validate([
            'upi_id'      => 'nullable|string|max:255',
            'scanner_qr'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->filled('upi_id')) {
            $event->upi_id = trim($request->upi_id);
        }

        if ($request->hasFile('scanner_qr')) {
            // Delete old QR image if any
            if ($event->gpay_qr_image && Storage::disk('public')->exists($event->gpay_qr_image)) {
                Storage::disk('public')->delete($event->gpay_qr_image);
            }
            $event->gpay_qr_image = $request->file('scanner_qr')->store('ganpati_qr', 'public');
        }

        $event->save();

        return redirect()->route('client.ganpati.show', $event->id)
            ->with('success', 'Scanner / UPI details saved.');
    }

    public function qr($id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);

        if (!$event->upi_id) {
            abort(404);
        }

        $upiUrl = 'upi://pay?pa=' . urlencode($event->upi_id)
            . '&pn=' . urlencode($event->title)
            . '&cu=INR';

        $svg = QrCode::size(400)->generate($upiUrl);

        return response($svg)->header('Content-Type', 'image/svg+xml; charset=utf-8')
            ->header('Cache-Control', 'no-store');
    }

    // ── Chandla (Chanda) CRUD ────────────────────────────────────────────────

    public function chandlaCreate($id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);
        return view('client.ganpati.chandla-create', compact('event'));
    }

    public function chandlaStore(Request $request, $id)
    {
        $event = $this->userGanpatiEvents()->findOrFail($id);

        $validated = $request->validate([
            'giver_name'         => 'required|string|max:255',
            'giver_phone'        => 'required|string|max:30',
            'giver_address'      => 'nullable|string',
            'amount'             => 'required|numeric|min:0',
            'payment_method'     => 'required|in:cash,gpay,other',
            'gpay_transaction_id'=> 'nullable|string|max:255',
            'gpay_image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'received_date'      => 'required|date',
            'notes'              => 'nullable|string',
            'cash_note_500'      => 'nullable|integer|min:0',
            'cash_note_200'      => 'nullable|integer|min:0',
            'cash_note_100'      => 'nullable|integer|min:0',
            'cash_note_50'       => 'nullable|integer|min:0',
            'cash_note_20'       => 'nullable|integer|min:0',
            'cash_note_10'       => 'nullable|integer|min:0',
            'cash_note_5'        => 'nullable|integer|min:0',
            'cash_note_2'        => 'nullable|integer|min:0',
            'cash_note_1'        => 'nullable|integer|min:0',
        ]);

        $cashNotes = $this->extractNotes($validated, 'cash_note_');
        $changeAmount = 0;
        $changeNotes  = $this->emptyNotes();
        $changeStatus = null;

        if ($validated['payment_method'] === 'cash') {
            $receivedTotal = $this->sumNotes($cashNotes);
            if ($receivedTotal <= 0) {
                return back()->withErrors(['amount' => 'Please enter cash note quantities.'])->withInput();
            }
            if (!$this->isWholeNumber($validated['amount'])) {
                return back()->withErrors(['amount' => 'Amount must be a whole number for cash.'])->withInput();
            }
            $amount = (int) $validated['amount'];
            if ($receivedTotal < $amount) {
                return back()->withErrors(['amount' => 'Received cash is less than the chanda amount.'])->withInput();
            }
            $changeAmount = $receivedTotal - $amount;
            $changeStatus = 'returned';
        }

        $gpayImage = null;
        if ($validated['payment_method'] === 'gpay' && $request->hasFile('gpay_image')) {
            $gpayImage = $request->file('gpay_image')->store('gpay_images', 'public');
        }

        $data = [
            'user_id'             => $event->user_id,
            'event_id'            => $event->id,
            'giver_name'          => $validated['giver_name'],
            'giver_phone'         => $validated['giver_phone'] ?? null,
            'giver_address'       => $validated['giver_address'] ?? null,
            'category'            => 'chandla',
            'payment_method'      => $validated['payment_method'],
            'amount'              => $validated['amount'],
            'gpay_transaction_id' => $validated['payment_method'] === 'gpay' ? ($validated['gpay_transaction_id'] ?? null) : null,
            'gpay_image'          => $gpayImage,
            'received_date'       => $validated['received_date'],
            'notes'               => $validated['notes'] ?? null,
            'change_amount'       => $changeAmount,
            'change_status'       => $changeStatus,
        ];

        // Add denomination columns
        foreach ($this->denominations() as $d) {
            $data['cash_note_' . $d]   = $cashNotes[$d] ?? 0;
            $data['change_note_' . $d] = $changeNotes[$d] ?? 0;
        }

        // Update cash inventory for cash entries
        $chandla = DB::transaction(function () use ($data, $cashNotes, $changeAmount, $event) {
            if ($data['payment_method'] === 'cash') {
                $inventory = $this->getInventoryForUpdate($event->id);
                $available = $this->addNotes($this->extractNotes($inventory->toArray(), 'note_'), $cashNotes);

                if ($changeAmount > 0) {
                    $res = $this->calculateChangeNotes((int) $changeAmount, $available);
                    if ($res['remaining'] > 0) {
                        $data['change_status'] = 'pending';
                        $changeNotes = $this->emptyNotes();
                        $newInventory = $available;
                    } else {
                        $data['change_status'] = 'returned';
                        $changeNotes = $res['notes'];
                        $newInventory = $this->subtractNotes($available, $changeNotes);
                    }
                    foreach ($this->denominations() as $d) {
                        $data['change_note_' . $d] = $changeNotes[$d] ?? 0;
                    }
                } else {
                    $newInventory = $available;
                }

                foreach ($this->denominations() as $d) {
                    $inventory->{'note_' . $d} = $newInventory[$d];
                }
                $inventory->save();
            }

            return Chandla::create($data);
        });

        if (!empty($chandla->giver_phone)) {
            try {
                $waService = new \App\Services\WhatsAppService();
                $cleanPhone = preg_replace('/^\+?91/', '', $chandla->giver_phone);
                $waService->sendTemplateMessage(
                    to: '91' . $cleanPhone,
                    templateName: 'chandla_added',
                    languageCode: 'en',
                    components: [
                        [
                            'type' => 'body',
                            'parameters' => [
                                \App\Services\WhatsAppService::formatTextParameter($chandla->giver_name ?? 'Guest'),
                                \App\Services\WhatsAppService::formatTextParameter((string) ($chandla->amount ?? 0))
                            ]
                        ]
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('WhatsApp chandla_added failed in ganpati', [
                    'chandla_id' => $chandla->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($request->input('submit_action') === 'another') {
            return redirect()->route('client.ganpati.chandla.create', $event->id)
                ->with('success', 'Entry saved! Add another chanda entry.');
        }

        return redirect()->route('client.ganpati.show', $event->id)
            ->with('success', 'Chanda entry recorded successfully.');
    }

    public function chandlaEdit($id, $chandlaId)
    {
        $event   = $this->userGanpatiEvents()->findOrFail($id);
        $chandla = Chandla::where('event_id', $event->id)
            ->whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($chandlaId);

        return view('client.ganpati.chandla-edit', compact('event', 'chandla'));
    }

    public function chandlaUpdate(Request $request, $id, $chandlaId)
    {
        $event   = $this->userGanpatiEvents()->findOrFail($id);
        $chandla = Chandla::where('event_id', $event->id)
            ->whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($chandlaId);

        $validated = $request->validate([
            'giver_name'         => 'required|string|max:255',
            'giver_phone'        => 'nullable|string|max:30',
            'giver_address'      => 'nullable|string',
            'amount'             => 'required|numeric|min:0',
            'payment_method'     => 'required|in:cash,gpay,other',
            'gpay_transaction_id'=> 'nullable|string|max:255',
            'gpay_image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'received_date'      => 'required|date',
            'notes'              => 'nullable|string',
        ]);

        $updateData = [
            'giver_name'          => $validated['giver_name'],
            'giver_phone'         => $validated['giver_phone'] ?? null,
            'giver_address'       => $validated['giver_address'] ?? null,
            'amount'              => $validated['amount'],
            'payment_method'      => $validated['payment_method'],
            'gpay_transaction_id' => $validated['payment_method'] === 'gpay' ? ($validated['gpay_transaction_id'] ?? null) : null,
            'received_date'       => $validated['received_date'],
            'notes'               => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('gpay_image')) {
            if ($chandla->gpay_image && Storage::disk('public')->exists($chandla->gpay_image)) {
                Storage::disk('public')->delete($chandla->gpay_image);
            }
            $updateData['gpay_image'] = $request->file('gpay_image')->store('gpay_images', 'public');
        } elseif ($validated['payment_method'] !== 'gpay') {
            if ($chandla->gpay_image && Storage::disk('public')->exists($chandla->gpay_image)) {
                Storage::disk('public')->delete($chandla->gpay_image);
            }
            $updateData['gpay_image'] = null;
        }

        $chandla->update($updateData);

        return redirect()->route('client.ganpati.show', $event->id)
            ->with('success', 'Chanda entry updated successfully.');
    }

    public function chandlaDestroy($id, $chandlaId)
    {
        $event   = $this->userGanpatiEvents()->findOrFail($id);
        $chandla = Chandla::where('event_id', $event->id)
            ->whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($chandlaId);

        DB::transaction(function () use ($chandla) {
            $effect = $this->getInventoryEffect($chandla);
            if ($effect['is_cash']) {
                $inventory     = $this->getInventoryForUpdate($chandla->event_id);
                $inventoryNotes = $this->extractNotes($inventory->toArray(), 'note_');
                $inventoryNotes = $this->subtractNotes($inventoryNotes, $effect['received']);
                $inventoryNotes = $this->addNotes($inventoryNotes, $effect['change']);
                foreach ($this->denominations() as $d) {
                    $inventory->{'note_' . $d} = max(0, $inventoryNotes[$d]);
                }
                $inventory->save();
            }
            $chandla->delete();
        });

        return redirect()->route('client.ganpati.show', $event->id)
            ->with('success', 'Entry deleted.');
    }

    // ── PDF ───────────────────────────────────────────────────────────────────

    public function pdf($id)
    {
        $event = $this->userGanpatiEvents()
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
            $dompdf     = $pdf->getDomPDF();
            $canvas     = $dompdf->getCanvas();
            $fontMetrics= $dompdf->getFontMetrics();
            $font       = $fontMetrics->get_font('DejaVu Sans', 'normal');
            if ($font) {
                $muted = [0.38, 0.41, 0.45];
                $canvas->page_text(34, 806, 'Chandla Book · Ganpati Special', $font, 7.5, $muted);
                $canvas->page_text(238, 806, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 9, [0.18, 0.23, 0.29]);
            }
        } catch (\Throwable $e) {
            Log::warning('Ganpati PDF footer skipped: ' . $e->getMessage());
        }

        $filename = 'ganpati-chanda-' . $event->id . '.pdf';

        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
