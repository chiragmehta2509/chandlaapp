<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function generate($eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($eventId);

        // Generate payment URL
        $paymentUrl = route('public.payment', ['event' => $event->id, 'token' => $event->payment_token ?? 'default']);

        // Generate QR code
        $qrCode = QrCode::size(400)
            ->format('png')
            ->generate($paymentUrl);

        return response($qrCode)
            ->header('Content-Type', 'image/png');
    }

    public function show($eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->with('chandlas')
            ->findOrFail($eventId);

        $paymentUrl = route('public.payment', ['event' => $event->id, 'token' => $event->payment_token ?? 'default']);

        return view('client.qrcode.show', compact('event', 'paymentUrl'));
    }

    public function download($eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($eventId);

        $paymentUrl = route('public.payment', ['event' => $event->id, 'token' => $event->payment_token ?? 'default']);

        $qrCode = QrCode::size(1000)
            ->format('png')
            ->generate($paymentUrl);

        $filename = 'gpay-qr-' . $event->id . '-' . time() . '.png';

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
