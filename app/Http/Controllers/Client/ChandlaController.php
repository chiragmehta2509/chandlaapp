<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Contact;
use App\Models\EventCashInventory;
use App\Models\Event;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChandlaController extends Controller
{
    public function index(Request $request)
    {
        $query = Chandla::whereIn('user_id', Auth::user()->allowedUserIds())
            ->with('event');

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
            try {
                $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('received_date', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Ignore invalid date formats
            }
        }

        $chandlas = $query->orderBy('received_date', 'desc')->get();
        $events = Event::whereIn('user_id', Auth::user()->allowedUserIds())->orderBy('event_date', 'desc')->get();

        return view('client.chandlas.index', compact('chandlas', 'events'));
    }

    public function create(Request $request)
    {
        $events = Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->where('is_archived', false)
            ->orderBy('event_date', 'desc')
            ->get();

        $eventId = $request->event_id;
        $lockCashMode = $request->boolean('lock_cash');
        $defaultCategory = $lockCashMode ? 'cover' : $request->query('category');
        $defaultPaymentMethod = $lockCashMode ? 'cash' : $request->query('payment_method');
        $defaultGiverName = trim((string) $request->query('giver_name', ''));

        if (!in_array($defaultCategory, ['chandla', 'cover', 'gift'], true)) {
            $defaultCategory = null;
        }
        if (!in_array($defaultPaymentMethod, ['cash', 'gpay', 'hard_form', 'other'], true)) {
            $defaultPaymentMethod = null;
        }

        $giverNameOptions = collect();
        if ($lockCashMode && $eventId) {
            $eventOwnedByUser = Event::where('id', $eventId)
                ->whereIn('user_id', Auth::user()->allowedUserIds())
                ->exists();

            if ($eventOwnedByUser) {
                $giverNameOptions = Chandla::whereIn('user_id', Auth::user()->allowedUserIds())
                    ->where('event_id', $eventId)
                    ->where('category', 'cover')
                    ->whereNotNull('giver_name')
                    ->where('giver_name', '!=', '')
                    ->select('giver_name')
                    ->distinct()
                    ->orderBy('giver_name')
                    ->pluck('giver_name');
            }
        }

        return view('client.chandlas.create', compact(
            'events',
            'eventId',
            'lockCashMode',
            'defaultCategory',
            'defaultPaymentMethod',
            'defaultGiverName',
            'giverNameOptions'
        ));
    }

    public function lookupGiver(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'nullable|integer',
            'giver_name' => 'required|string|max:255',
            'category' => 'nullable|in:chandla,cover,gift',
        ]);

        $normalizedName = strtolower(trim($validated['giver_name']));

        $query = Chandla::whereIn('user_id', Auth::user()->allowedUserIds())
            ->whereRaw('LOWER(TRIM(giver_name)) = ?', [$normalizedName]);

        // Scope to event if provided and valid
        if (!empty($validated['event_id'])) {
            $event = Event::where('id', $validated['event_id'])
                ->whereIn('user_id', Auth::user()->allowedUserIds())
                ->first();
            if ($event) {
                $query->where('event_id', $event->id);
            }
        }

        $previous = $query
            ->when(!empty($validated['category']), function ($q) use ($validated) {
                $q->where('category', $validated['category']);
            })
            ->orderByDesc('received_date')
            ->orderByDesc('id')
            ->first();

        if (!$previous) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'giver_name' => $previous->giver_name,
            'amount' => $previous->amount,
            'giver_address' => $previous->giver_address,
            'giver_phone' => $previous->giver_phone,
            'giver_email' => $previous->giver_email,
            'received_date' => optional($previous->received_date)->format('Y-m-d'),
        ]);
    }

    public function searchGivers(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|max:255',
        ]);

        $searchQuery = trim($validated['q']);
        if ($searchQuery === '') {
            return response()->json(['items' => []]);
        }

        $userIds = Auth::user()->allowedUserIds();

        // Search from the dedicated Contacts table first
        $contacts = Contact::whereIn('user_id', $userIds)
            ->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('phone', 'like', '%' . $searchQuery . '%');
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['name', 'phone', 'email', 'address']);

        $items = [];
        $seen = [];

        foreach ($contacts as $contact) {
            $key = strtolower(trim((string) $contact->name));
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $items[] = [
                'giver_name'    => $contact->name,
                'giver_phone'   => $contact->phone,
                'giver_email'   => $contact->email,
                'giver_address' => $contact->address,
            ];
        }

        // If fewer than 8, also pull unique names from chandla history
        if (count($items) < 8) {
            $rows = Chandla::whereIn('user_id', $userIds)
                ->where('giver_name', 'like', '%' . $searchQuery . '%')
                ->orderByDesc('received_date')
                ->orderByDesc('id')
                ->limit(50)
                ->get(['giver_name', 'giver_phone', 'giver_email', 'giver_address']);

            foreach ($rows as $row) {
                $key = strtolower(trim((string) $row->giver_name));
                if ($key === '' || isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $items[] = [
                    'giver_name'    => $row->giver_name,
                    'giver_phone'   => $row->giver_phone,
                    'giver_email'   => $row->giver_email,
                    'giver_address' => $row->giver_address,
                ];
                if (count($items) >= 8) {
                    break;
                }
            }
        }

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $updateExistingCover = $request->boolean('update_existing_cover');

        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'giver_name' => 'required|string|max:255',
            'giver_phone' => 'required|string',
            'giver_email' => 'nullable|email',
            'giver_address' => 'nullable|string',
            'category' => 'required|in:chandla,cover,gift',
            'payment_method' => 'nullable|in:cash,gpay,hard_form,other',
            'gpay_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gpay_transaction_id' => 'nullable|required_if:payment_method,gpay|string|max:255',
            'amount' => 'required_if:category,chandla|nullable|numeric|min:0',
            'gift_item_name' => 'nullable|required_if:category,gift|string|max:255',
            'gift_received' => 'nullable|required_if:category,gift|boolean',
            'cash_note_1' => 'nullable|integer|min:0',
            'cash_note_2' => 'nullable|integer|min:0',
            'cash_note_5' => 'nullable|integer|min:0',
            'cash_note_10' => 'nullable|integer|min:0',
            'cash_note_20' => 'nullable|integer|min:0',
            'cash_note_50' => 'nullable|integer|min:0',
            'cash_note_100' => 'nullable|integer|min:0',
            'cash_note_200' => 'nullable|integer|min:0',
            'cash_note_500' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'received_date' => 'required|date',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Verify event belongs to user
        $event = Event::with('user')->whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($validated['event_id']);
        $coverToUpdate = null;
        if ($updateExistingCover && $validated['category'] === 'cover') {
            $normalizedName = strtolower(trim((string) $validated['giver_name']));
            if ($normalizedName !== '') {
                $coverToUpdate = Chandla::where('user_id', $event->user_id)
                    ->where('event_id', $validated['event_id'])
                    ->where('category', 'cover')
                    ->whereRaw('LOWER(TRIM(giver_name)) = ?', [$normalizedName])
                    ->orderByDesc('id')
                    ->first();
            }
        }

        $hasPaidPlan = $event->user->planLevel() >= 2;
        $globalFreeUsedEntries = Chandla::where('user_id', $event->user_id)->count();
        if (! $hasPaidPlan && $globalFreeUsedEntries >= 50 && ! $coverToUpdate) {
            return back()->withErrors([
                'pricing_plan' => 'Free plan allows only 50 total entries. Please purchase Unlimited to add more entries.',
            ])->withInput();
        }

        $currentCount = $event->chandlas()->count();
        $freeLimit = min((int) ($event->free_entry_limit ?? 50), 50);
        if (($event->pricing_plan ?? 'free') === 'free' && $currentCount >= $freeLimit && ! $coverToUpdate) {
            if (! $event->user->hasLedgerUnlimitedChandla()) {
                return back()->withErrors([
                    'pricing_plan' => 'Free limit reached for this event. Upgrade to Pay-as-you-go or Unlimited to add more entries.',
                ])->withInput();
            }
        }

        $cashNotes = $this->extractNotes($validated, 'cash_note_');
        foreach ($this->denominations() as $denomination) {
            $validated['cash_note_' . $denomination] = $cashNotes[$denomination];
        }

        $validated['change_amount'] = 0;
        $validated['change_status'] = null;
        foreach ($this->denominations() as $denomination) {
            $validated['change_note_' . $denomination] = 0;
        }

        if ($validated['category'] === 'gift') {
            $validated['payment_method'] = 'other';
            $validated['gpay_transaction_id'] = null;
            foreach ($this->denominations() as $denomination) {
                $validated['cash_note_' . $denomination] = 0;
            }
            $validated['amount'] = 0;
        } elseif ($validated['category'] === 'cover') {
            $validated['gift_item_name'] = null;
            $validated['gift_received'] = null;
            $validated['gpay_transaction_id'] = null;

            if (($validated['payment_method'] ?? null) === 'cash') {
                $receivedTotal = $this->sumNotes($cashNotes);

                if ($receivedTotal <= 0) {
                    return back()->withErrors(['amount' => 'Please enter cash note quantities for cover entries.'])->withInput();
                }

                // Cover in cash mode should always store the received total.
                $validated['amount'] = $receivedTotal;
            } else {
                foreach ($this->denominations() as $denomination) {
                    $validated['cash_note_' . $denomination] = 0;
                }
                $validated['amount'] = $validated['amount'] ?? 0;
            }
        } else {
            $validated['gift_item_name'] = null;
            $validated['gift_received'] = null;
            if (empty($validated['payment_method'])) {
                return back()->withErrors(['payment_method' => 'Payment method is required for cash contributions.'])->withInput();
            }

            if ($validated['payment_method'] === 'cash') {
                $receivedTotal = $this->sumNotes($cashNotes);

                if ($receivedTotal <= 0) {
                    return back()->withErrors(['amount' => 'Please enter cash note quantities for the received amount.'])->withInput();
                }

                if ($validated['amount'] === null) {
                    return back()->withErrors(['amount' => 'Amount is required for cash contributions.'])->withInput();
                }

                if (!$this->isWholeNumber($validated['amount'])) {
                    return back()->withErrors(['amount' => 'Amount must be a whole number for cash transactions.'])->withInput();
                }

                $amount = (int) $validated['amount'];
                if ($receivedTotal < $amount) {
                    return back()->withErrors(['amount' => 'Received cash is less than the contribution amount.'])->withInput();
                }

                $validated['change_amount'] = $receivedTotal - $amount;
                $validated['gpay_transaction_id'] = null;
            } else {
                foreach ($this->denominations() as $denomination) {
                    $validated['cash_note_' . $denomination] = 0;
                }
            }
        }

        if (($validated['payment_method'] ?? null) !== 'gpay') {
            $validated['gpay_transaction_id'] = null;
        }

        if (($validated['payment_method'] ?? null) === 'gpay' && $request->hasFile('gpay_image')) {
            $imagePath = $request->file('gpay_image')->store('gpay_images', 'public');

            if (class_exists('Intervention\Image\Facades\Image')) {
                try {
                    $image = \Intervention\Image\Facades\Image::make(storage_path('app/public/' . $imagePath));
                    if ($image->width() > 1920) {
                        $image->resize(1920, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    $image->save(storage_path('app/public/' . $imagePath), 85);
                } catch (\Exception $e) {
                    // Continue if image processing fails
                }
            }

            $validated['gpay_image'] = $imagePath;
        } else {
            $validated['gpay_image'] = null;
        }

        $validated['user_id'] = $event->user_id;

        $chandla = DB::transaction(function () use ($validated, $cashNotes, $coverToUpdate) {
            if ($validated['category'] === 'chandla' && ($validated['payment_method'] ?? null) === 'cash') {
                $inventory = $this->getInventoryForUpdate($validated['event_id']);
                $availableNotes = $this->addNotes($this->extractNotes($inventory->toArray(), 'note_'), $cashNotes);

                if ($validated['change_amount'] > 0) {
                    $changeResult = $this->calculateChangeNotes((int) $validated['change_amount'], $availableNotes);
                    if ($changeResult['remaining'] > 0) {
                        $validated['change_status'] = 'pending';
                        $validated['change_amount'] = $changeResult['requested'];
                        $changeNotes = $this->emptyNotes();
                        $newInventory = $availableNotes;
                    } else {
                        $validated['change_status'] = 'returned';
                        $changeNotes = $changeResult['notes'];
                        $newInventory = $this->subtractNotes($availableNotes, $changeNotes);
                    }
                } else {
                    $validated['change_status'] = 'returned';
                    $changeNotes = $this->emptyNotes();
                    $newInventory = $availableNotes;
                }

                foreach ($this->denominations() as $denomination) {
                    $validated['change_note_' . $denomination] = $changeNotes[$denomination];
                    $inventory->{'note_' . $denomination} = $newInventory[$denomination];
                }

                $inventory->save();
            }

            if ($coverToUpdate) {
                $coverToUpdate->update($validated);
                return $coverToUpdate->fresh();
            }

            return Chandla::create($validated);
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
                \Illuminate\Support\Facades\Log::error('WhatsApp chandla_added failed in web', [
                    'chandla_id' => $chandla->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = $coverToUpdate ? 'Cover data updated successfully' : 'Chandla record created successfully';

        $chandlaSavedSummary = [
            'giver_name' => (string) ($chandla->giver_name ?? ''),
            'amount' => (float) ($chandla->amount ?? 0),
            'giver_address' => $chandla->giver_address !== null && trim((string) $chandla->giver_address) !== ''
                ? (string) $chandla->giver_address
                : null,
            'category' => (string) ($chandla->category ?? ''),
        ];

        if ($request->input('submit_action') === 'another') {
            $params = ['event_id' => $validated['event_id']];
            if ($request->boolean('from_lock_cash')) {
                $params['lock_cash'] = '1';
            }

            return redirect()->route('client.chandlas.create', $params)
                ->with('success', $message)
                ->with('chandla_saved_summary', $chandlaSavedSummary);
        }

        return redirect()->route('client.chandlas.show', $chandla->id)
            ->with('success', $message)
            ->with('chandla_saved_summary', $chandlaSavedSummary);
    }

    public function show($id)
    {
        $chandla = Chandla::with('event')
            ->whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($id);

        return view('client.chandlas.show', compact('chandla'));
    }

    public function pdf($eventId)
    {
        $event = Event::with('chandlas')
            ->whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($eventId);

        $inventory = EventCashInventory::firstOrCreate(
            ['event_id' => $event->id],
            [
                'note_1' => 0,
                'note_2' => 0,
                'note_5' => 0,
                'note_10' => 0,
                'note_20' => 0,
                'note_50' => 0,
                'note_100' => 0,
                'note_200' => 0,
                'note_500' => 0,
            ]
        );

        $cash = $event->chandlas->where('category', 'chandla')->where('payment_method', '!=', 'gpay')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $gpay = $event->chandlas->where('payment_method', 'gpay')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $cover = $event->chandlas->where('category', 'cover')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $gift = $event->chandlas->where('category', 'gift')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $gujaratiFontPath = $this->resolvePdfGujaratiFontPath();

        $pdf = Pdf::loadView('client.chandlas.pdf', [
            'event'            => $event,
            'inventory'        => $inventory,
            'cash'             => $cash,
            'gpay'             => $gpay,
            'cover'            => $cover,
            'gift'             => $gift,
            'gujaratiFontPath' => $gujaratiFontPath,
        ]);

        $this->decorateEventChandlaPdf($pdf);

        $filename = 'event-chandla-' . $event->id . '.pdf';
        $pdfContent = $pdf->output();

        if (Auth::user()->email) {
            try {
                Mail::send('emails.event-pdf', [
                    'event' => $event,
                    'user' => Auth::user(),
                ], function ($message) use ($filename, $pdfContent, $event) {
                    $safeTitle = mb_strlen($event->title) > 60 ? mb_substr($event->title, 0, 57).'…' : $event->title;
                    $message->to(Auth::user()->email)
                        ->subject('Chandla register PDF · '.$safeTitle)
                        ->attachData($pdfContent, $filename, ['mime' => 'application/pdf']);
                });
            } catch (\Throwable $e) {
                // Do not block PDF download if email delivery fails.
                Log::error('Failed to send event PDF email', [
                    'event_id' => $event->id,
                    'user_id' => Auth::user()->dataOwnerId(),
                    'email' => Auth::user()->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Running footer: page numbers + subtle branding on every sheet (DomPDF canvas).
     */
    private function decorateEventChandlaPdf(\Barryvdh\DomPDF\PDF $pdf): void
    {
        try {
            $dompdf = $pdf->getDomPDF();
            $canvas = $dompdf->getCanvas();
            $fontMetrics = $dompdf->getFontMetrics();
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            if (!$font) {
                return;
            }

            $muted = [0.38, 0.41, 0.45];
            $canvas->page_text(34, 806, 'Chandla Book · '.config('app.name'), $font, 7.5, $muted);
            $canvas->page_text(238, 806, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 9, [0.18, 0.23, 0.29]);
        } catch (\Throwable $e) {
            Log::warning('Event PDF footer chrome skipped', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function freeLimitDownload()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $ownerId = $authUser->dataOwnerId();
        if ($authUser->isFamilyMember() && Event::where('user_id', $authUser->id)->exists()) {
            $ownerId = $authUser->id;
        }

        $owner = User::find($ownerId) ?? $authUser;

        $entries = Chandla::with('event')
            ->where('user_id', $ownerId)
            ->orderBy('received_date', 'asc')
            ->orderBy('id', 'asc')
            ->limit(50)
            ->get();

        $pdf = Pdf::loadView('client.chandlas.free-limit-pdf', [
            'entries' => $entries,
            'user' => $owner,
        ]);

        return $pdf->download('free-plan-first-50-entries.pdf');
    }

    public function ledgerPdf()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $ownerId = $authUser->dataOwnerId();
        if ($authUser->isFamilyMember() && Event::where('user_id', $authUser->id)->exists()) {
            $ownerId = $authUser->id;
        }

        $owner = User::find($ownerId) ?? $authUser;

        $entries = Chandla::with('event')
            ->where('user_id', $ownerId)
            ->orderBy('received_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $cash = $entries->where('category', 'chandla')->where('payment_method', '!=', 'gpay')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $gpay = $entries->where('payment_method', 'gpay')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $cover = $entries->where('category', 'cover')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $gift = $entries->where('category', 'gift')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
            
        $gujaratiFontPath = $this->resolvePdfGujaratiFontPath();

        $pdf = Pdf::loadView('client.chandlas.ledger-pdf', [
            'cash'             => $cash,
            'gpay'             => $gpay,
            'cover'            => $cover,
            'gift'             => $gift,
            'user'             => $owner,
            'gujaratiFontPath' => $gujaratiFontPath,
        ]);

        $this->decorateEventChandlaPdf($pdf);

        return $pdf->download('entire-ledger.pdf');
    }

    private function resolvePdfGujaratiFontPath(): ?string
    {
        $fontDirectory = storage_path('fonts');
        $projectFontPath = $fontDirectory . DIRECTORY_SEPARATOR . 'gujarati.ttf';

        if (is_file($projectFontPath)) {
            return $projectFontPath;
        }

        $sourceCandidates = [
            'C:\\Windows\\Fonts\\shruti.ttf',
            'C:\\Windows\\Fonts\\Nirmala.ttc',
            '/usr/share/fonts/truetype/noto/NotoSansGujarati-Regular.ttf',
            '/usr/share/fonts/truetype/lohit-gujarati/Lohit-Gujarati.ttf',
        ];

        foreach ($sourceCandidates as $candidate) {
            if (!is_file($candidate)) {
                continue;
            }

            if (!is_dir($fontDirectory)) {
                @mkdir($fontDirectory, 0775, true);
            }

            if (@copy($candidate, $projectFontPath)) {
                return $projectFontPath;
            }
        }

        return null;
    }

    public function edit($id)
    {
        $chandla = Chandla::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $events = Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->where('is_archived', false)
            ->orderBy('event_date', 'desc')
            ->get();

        return view('client.chandlas.edit', compact('chandla', 'events'));
    }

    public function update(Request $request, $id)
    {
        $chandla = Chandla::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'giver_name' => 'required|string|max:255',
            'giver_phone' => 'nullable|string',
            'giver_email' => 'nullable|email',
            'giver_address' => 'nullable|string',
            'category' => 'required|in:chandla,cover,gift',
            'payment_method' => 'nullable|in:cash,gpay,hard_form,other',
            'gpay_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gpay_transaction_id' => 'nullable|required_if:payment_method,gpay|string|max:255',
            'amount' => 'required_if:category,chandla|nullable|numeric|min:0',
            'gift_item_name' => 'nullable|required_if:category,gift|string|max:255',
            'gift_received' => 'nullable|required_if:category,gift|boolean',
            'cash_note_1' => 'nullable|integer|min:0',
            'cash_note_2' => 'nullable|integer|min:0',
            'cash_note_5' => 'nullable|integer|min:0',
            'cash_note_10' => 'nullable|integer|min:0',
            'cash_note_20' => 'nullable|integer|min:0',
            'cash_note_50' => 'nullable|integer|min:0',
            'cash_note_100' => 'nullable|integer|min:0',
            'cash_note_200' => 'nullable|integer|min:0',
            'cash_note_500' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'received_date' => 'required|date',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Verify event belongs to user
        $event = Event::with('user')->whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($validated['event_id']);
        $currentCount = $event->chandlas()->where('id', '!=', $chandla->id)->count();
        $freeLimit = min((int) ($event->free_entry_limit ?? 50), 50);
        if (($event->pricing_plan ?? 'free') === 'free' && $currentCount >= $freeLimit) {
            if (! $event->user->hasLedgerUnlimitedChandla()) {
                return back()->withErrors([
                    'pricing_plan' => 'Free limit reached for this event. Upgrade to Pay-as-you-go or Unlimited to add more entries.',
                ])->withInput();
            }
        }

        $cashNotes = $this->extractNotes($validated, 'cash_note_');
        foreach ($this->denominations() as $denomination) {
            $validated['cash_note_' . $denomination] = $cashNotes[$denomination];
        }

        $validated['change_amount'] = 0;
        $validated['change_status'] = null;
        foreach ($this->denominations() as $denomination) {
            $validated['change_note_' . $denomination] = 0;
        }

        if ($validated['category'] !== 'chandla') {
            $validated['payment_method'] = 'other';
            $validated['gpay_transaction_id'] = null;
            foreach ($this->denominations() as $denomination) {
                $validated['cash_note_' . $denomination] = 0;
            }

            if ($validated['category'] === 'gift') {
                $validated['amount'] = 0;
            } else {
                $validated['gift_item_name'] = null;
                $validated['gift_received'] = null;
                $validated['amount'] = $validated['amount'] ?? 0;
            }
        } else {
            $validated['gift_item_name'] = null;
            $validated['gift_received'] = null;
            if (empty($validated['payment_method'])) {
                return back()->withErrors(['payment_method' => 'Payment method is required for cash contributions.'])->withInput();
            }

            if ($validated['payment_method'] === 'cash') {
                $receivedTotal = $this->sumNotes($cashNotes);

                if ($receivedTotal <= 0) {
                    return back()->withErrors(['amount' => 'Please enter cash note quantities for the received amount.'])->withInput();
                }

                if ($validated['amount'] === null) {
                    return back()->withErrors(['amount' => 'Amount is required for cash contributions.'])->withInput();
                }

                if (!$this->isWholeNumber($validated['amount'])) {
                    return back()->withErrors(['amount' => 'Amount must be a whole number for cash transactions.'])->withInput();
                }

                $amount = (int) $validated['amount'];
                if ($receivedTotal < $amount) {
                    return back()->withErrors(['amount' => 'Received cash is less than the contribution amount.'])->withInput();
                }

                $validated['change_amount'] = $receivedTotal - $amount;
                $validated['gpay_transaction_id'] = null;
            } else {
                foreach ($this->denominations() as $denomination) {
                    $validated['cash_note_' . $denomination] = 0;
                }
            }
        }

        if (($validated['payment_method'] ?? null) !== 'gpay') {
            $validated['gpay_transaction_id'] = null;
        }

        if (($validated['payment_method'] ?? null) === 'gpay') {
            if ($request->hasFile('gpay_image')) {
                if ($chandla->gpay_image && \Storage::disk('public')->exists($chandla->gpay_image)) {
                    \Storage::disk('public')->delete($chandla->gpay_image);
                }

                $imagePath = $request->file('gpay_image')->store('gpay_images', 'public');

                if (class_exists('Intervention\Image\Facades\Image')) {
                    try {
                        $image = \Intervention\Image\Facades\Image::make(storage_path('app/public/' . $imagePath));
                        if ($image->width() > 1920) {
                            $image->resize(1920, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }
                        $image->save(storage_path('app/public/' . $imagePath), 85);
                    } catch (\Exception $e) {
                        // Continue if image processing fails
                    }
                }

                $validated['gpay_image'] = $imagePath;
            }
        } else {
            if ($chandla->gpay_image && \Storage::disk('public')->exists($chandla->gpay_image)) {
                \Storage::disk('public')->delete($chandla->gpay_image);
            }
            $validated['gpay_image'] = null;
        }

        $updated = DB::transaction(function () use ($validated, $cashNotes, $chandla) {
            $oldEffect = $this->getInventoryEffect($chandla);

            if ($oldEffect['is_cash']) {
                $inventory = $this->getInventoryForUpdate($chandla->event_id);
                $inventoryNotes = $this->extractNotes($inventory->toArray(), 'note_');
                $inventoryNotes = $this->subtractNotes($inventoryNotes, $oldEffect['received']);
                $inventoryNotes = $this->addNotes($inventoryNotes, $oldEffect['change']);
                if ($this->hasNegativeNotes($inventoryNotes)) {
                    return ['error' => 'Inventory adjustment failed. Please update inventory and try again.'];
                }
                foreach ($this->denominations() as $denomination) {
                    $inventory->{'note_' . $denomination} = $inventoryNotes[$denomination];
                }
                $inventory->save();
            }

            if ($validated['category'] === 'chandla' && ($validated['payment_method'] ?? null) === 'cash') {
                $inventory = $this->getInventoryForUpdate($validated['event_id']);
                $availableNotes = $this->addNotes($this->extractNotes($inventory->toArray(), 'note_'), $cashNotes);

                if ($validated['change_amount'] > 0) {
                    $changeResult = $this->calculateChangeNotes((int) $validated['change_amount'], $availableNotes);
                    if ($changeResult['remaining'] > 0) {
                        $validated['change_status'] = 'pending';
                        $validated['change_amount'] = $changeResult['requested'];
                        $changeNotes = $this->emptyNotes();
                        $newInventory = $availableNotes;
                    } else {
                        $validated['change_status'] = 'returned';
                        $changeNotes = $changeResult['notes'];
                        $newInventory = $this->subtractNotes($availableNotes, $changeNotes);
                    }
                } else {
                    $validated['change_status'] = 'returned';
                    $changeNotes = $this->emptyNotes();
                    $newInventory = $availableNotes;
                }

                foreach ($this->denominations() as $denomination) {
                    $validated['change_note_' . $denomination] = $changeNotes[$denomination];
                    $inventory->{'note_' . $denomination} = $newInventory[$denomination];
                }
                $inventory->save();
            }

            $chandla->update($validated);

            return ['chandla' => $chandla];
        });

        if (isset($updated['error'])) {
            return back()->withErrors(['inventory' => $updated['error']])->withInput();
        }

        return redirect()->route('client.chandlas.show', $chandla->id)->with('success', 'Chandla record updated successfully');
    }

    public function destroy($id)
    {
        $chandla = Chandla::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $result = DB::transaction(function () use ($chandla) {
            $oldEffect = $this->getInventoryEffect($chandla);
            if ($oldEffect['is_cash']) {
                $inventory = $this->getInventoryForUpdate($chandla->event_id);
                $inventoryNotes = $this->extractNotes($inventory->toArray(), 'note_');
                $inventoryNotes = $this->subtractNotes($inventoryNotes, $oldEffect['received']);
                $inventoryNotes = $this->addNotes($inventoryNotes, $oldEffect['change']);
                if ($this->hasNegativeNotes($inventoryNotes)) {
                    return ['error' => 'Inventory adjustment failed. Please update inventory and try again.'];
                }
                foreach ($this->denominations() as $denomination) {
                    $inventory->{'note_' . $denomination} = $inventoryNotes[$denomination];
                }
                $inventory->save();
            }

            $chandla->delete();

            return ['success' => true];
        });

        if (isset($result['error'])) {
            return back()->withErrors(['inventory' => $result['error']]);
        }

        return redirect()->route('client.chandlas.index')->with('success', 'Chandla record deleted successfully');
    }

    public function clone($id)
    {
        $original = Chandla::where('user_id', Auth::user()->dataOwnerId())->findOrFail($id);

        $cloneData = $original->only([
            'event_id', 'user_id', 'giver_name', 'giver_phone', 'giver_email',
            'giver_address', 'category', 'payment_method', 'amount',
            'gift_item_name', 'gift_received', 'description', 'notes',
            'cash_note_1','cash_note_2','cash_note_5','cash_note_10','cash_note_20',
            'cash_note_50','cash_note_100','cash_note_200','cash_note_500',
        ]);

        $cloneData['received_date']       = now()->toDateString();
        $cloneData['gpay_image']          = null;
        $cloneData['gpay_transaction_id'] = null;
        $cloneData['change_amount']       = 0;
        $cloneData['change_status']       = null;
        $cloneData['receipt_number']      = null;
        $cloneData['is_verified']         = false;
        $cloneData['verified_at']         = null;
        foreach ($this->denominations() as $denomination) {
            $cloneData['change_note_' . $denomination] = 0;
        }

        Chandla::create($cloneData);

        return redirect()->route('client.chandlas.index')
            ->with('success', 'Entry cloned successfully — please review and update as needed.');
    }

    private function denominations(): array
    {
        return [500, 200, 100, 50, 20, 10, 5, 2, 1];
    }

    private function extractNotes(array $data, string $prefix): array
    {
        $notes = [];
        foreach ($this->denominations() as $denomination) {
            $notes[$denomination] = (int) ($data[$prefix . $denomination] ?? 0);
        }
        return $notes;
    }

    private function emptyNotes(): array
    {
        $notes = [];
        foreach ($this->denominations() as $denomination) {
            $notes[$denomination] = 0;
        }
        return $notes;
    }

    private function sumNotes(array $notes): int
    {
        $total = 0;
        foreach ($notes as $denomination => $count) {
            $total += $denomination * $count;
        }
        return $total;
    }

    private function addNotes(array $base, array $delta): array
    {
        foreach ($this->denominations() as $denomination) {
            $base[$denomination] = ($base[$denomination] ?? 0) + ($delta[$denomination] ?? 0);
        }
        return $base;
    }

    private function subtractNotes(array $base, array $delta): array
    {
        foreach ($this->denominations() as $denomination) {
            $base[$denomination] = ($base[$denomination] ?? 0) - ($delta[$denomination] ?? 0);
        }
        return $base;
    }

    private function hasNegativeNotes(array $notes): bool
    {
        foreach ($notes as $count) {
            if ($count < 0) {
                return true;
            }
        }
        return false;
    }

    private function calculateChangeNotes(int $changeAmount, array $availableNotes): array
    {
        $remaining = $changeAmount;
        $notes = $this->emptyNotes();

        foreach ($this->denominations() as $denomination) {
            if ($remaining <= 0) {
                break;
            }
            $available = $availableNotes[$denomination] ?? 0;
            if ($available <= 0) {
                continue;
            }
            $need = intdiv($remaining, $denomination);
            if ($need <= 0) {
                continue;
            }
            $use = min($need, $available);
            $notes[$denomination] = $use;
            $remaining -= $use * $denomination;
        }

        return [
            'requested' => $changeAmount,
            'remaining' => $remaining,
            'notes' => $notes,
        ];
    }

    private function isWholeNumber($value): bool
    {
        return is_numeric($value) && floor((float) $value) == (float) $value;
    }

    private function getInventoryForUpdate(int $eventId): EventCashInventory
    {
        $defaults = [
            'note_1' => 0,
            'note_2' => 0,
            'note_5' => 0,
            'note_10' => 0,
            'note_20' => 0,
            'note_50' => 0,
            'note_100' => 0,
            'note_200' => 0,
            'note_500' => 0,
        ];

        EventCashInventory::firstOrCreate(['event_id' => $eventId], $defaults);

        return EventCashInventory::where('event_id', $eventId)->lockForUpdate()->first();
    }

    private function getInventoryEffect(Chandla $chandla): array
    {
        $isCash = $chandla->category === 'chandla' && $chandla->payment_method === 'cash';
        if (!$isCash) {
            return [
                'is_cash' => false,
                'received' => $this->emptyNotes(),
                'change' => $this->emptyNotes(),
            ];
        }

        $received = [];
        $change = [];
        foreach ($this->denominations() as $denomination) {
            $received[$denomination] = (int) $chandla->{'cash_note_' . $denomination};
            $change[$denomination] = (int) $chandla->{'change_note_' . $denomination};
        }

        return [
            'is_cash' => true,
            'received' => $received,
            'change' => $change,
        ];
    }
}
