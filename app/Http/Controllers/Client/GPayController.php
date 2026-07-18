<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GPayController extends Controller
{
    public function showUploadForm(Request $request)
    {
        $eventId = $request->event_id;
        $event = null;
        $upiQrSvg = null;
        $upiQrUri = null;
        
        if ($eventId) {
            $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())
                ->where('is_archived', false)
                ->findOrFail($eventId);
            if ($event->upi_id && !$event->gpay_qr_image) {
                $upiQrUri = 'upi://pay?pa=' . urlencode($event->upi_id)
                    . '&pn=' . urlencode($event->title ?? 'Event')
                    . '&cu=INR';
                $upiQrSvg = QrCode::size(220)->generate($upiQrUri);
            }
        }
        
        $events = Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->where('is_archived', false)
            ->orderBy('event_date', 'desc')
            ->get();

        return view('client.gpay.upload', compact('events', 'event', 'eventId', 'upiQrSvg', 'upiQrUri'));
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'gpay_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'giver_name' => 'nullable|string|max:255',
            'giver_phone' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'gpay_transaction_id' => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Verify event belongs to user
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($validated['event_id']);

        // Store the image
        $imagePath = $request->file('gpay_image')->store('gpay_images', 'public');
        
        // Optionally resize/optimize the image if Intervention Image is available
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

        // Create chandla record
        $chandla = Chandla::create([
            'event_id' => $validated['event_id'],
            'user_id' => $event->user_id,
            'giver_name' => $validated['giver_name'] ?? 'From GPay',
            'giver_phone' => $validated['giver_phone'] ?? null,
            'category' => 'chandla',
            'payment_method' => 'gpay',
            'gpay_image' => $imagePath,
            'gpay_transaction_id' => $validated['gpay_transaction_id'] ?? null,
            'amount' => $validated['amount'] ?? 0,
            'received_date' => $validated['received_date'] ?? now()->toDateString(),
            'notes' => $validated['notes'] ?? 'Uploaded via GPay scanner',
        ]);

        return redirect()->route('client.chandlas.show', $chandla->id)
            ->with('success', 'GPay image uploaded successfully! Please review and update the details if needed.');
    }

    public function saveDetails(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'upi_id' => 'required|string|max:255',
            'gpay_qr_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($validated['event_id']);

        $imagePath = null;
        if ($request->hasFile('gpay_qr_image')) {
            $imagePath = $request->file('gpay_qr_image')->store('gpay_qr', 'public');

            if (class_exists('Intervention\Image\Facades\Image')) {
                try {
                    $image = \Intervention\Image\Facades\Image::make(storage_path('app/public/' . $imagePath));
                    if ($image->width() > 1200) {
                        $image->resize(1200, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    $image->save(storage_path('app/public/' . $imagePath), 85);
                } catch (\Exception $e) {
                    // Continue if image processing fails
                }
            }

            if ($event->gpay_qr_image && Storage::disk('public')->exists($event->gpay_qr_image)) {
                Storage::disk('public')->delete($event->gpay_qr_image);
            }
        }

        $event->upi_id = $validated['upi_id'];
        if ($imagePath) {
            $event->gpay_qr_image = $imagePath;
        }
        $event->save();

        return back()->with('success', 'GPay receiving details saved for this event.');
    }

    public function quickUpload(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'gpay_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Verify event belongs to user
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($validated['event_id']);

        // Store the image
        $imagePath = $request->file('gpay_image')->store('gpay_images', 'public');
        
        // Optimize image if Intervention Image is available
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

        // Create chandla record with minimal info
        $chandla = Chandla::create([
            'event_id' => $validated['event_id'],
            'user_id' => $event->user_id,
            'giver_name' => 'From GPay',
            'category' => 'chandla',
            'payment_method' => 'gpay',
            'gpay_image' => $imagePath,
            'amount' => 0,
            'received_date' => now()->toDateString(),
            'notes' => 'Quick upload via GPay scanner - Please update details',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'GPay image uploaded successfully',
            'chandla_id' => $chandla->id,
            'redirect_url' => route('client.chandlas.edit', $chandla->id),
        ]);
    }

    public function viewImage($id)
    {
        $chandla = Chandla::whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($id);

        if (!$chandla->gpay_image || !Storage::disk('public')->exists($chandla->gpay_image)) {
            abort(404, 'Image not found');
        }

        return response()->file(storage_path('app/public/' . $chandla->gpay_image));
    }

    public function upiQr($eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->where('is_archived', false)
            ->findOrFail($eventId);

        if (empty($event->upi_id)) {
            abort(404, 'UPI ID not configured');
        }

        $upiUri = 'upi://pay?pa=' . urlencode($event->upi_id)
            . '&pn=' . urlencode($event->title ?? 'Event')
            . '&cu=INR';

        $qrCode = QrCode::size(260)
            ->generate($upiUri);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}
