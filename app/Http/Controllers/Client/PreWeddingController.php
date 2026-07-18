<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PreWeddingAsset;
use App\Models\PreWeddingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class PreWeddingController extends Controller
{
    public function index()
    {
        $milestones = config('pre_wedding.milestones', []);
        $user = Auth::user();
        
        $userId = $user->id;
        if ($user->isFamilyMember() && !\App\Models\PreWeddingAsset::where('user_id', $userId)->exists() && !\App\Models\PreWeddingSetting::where('user_id', $userId)->exists()) {
            $userId = $user->parent_user_id;
        }

        $assets = PreWeddingAsset::where('user_id', $userId)->get()->keyBy('milestone_key');
        $setting = PreWeddingSetting::firstOrCreate(
            ['user_id' => $userId],
            ['wedding_date' => null]
        );
        $showDemoOnly = ! $user->hasCelebrationPackAccess();

        return view('client.pre-wedding.index', compact('milestones', 'assets', 'setting', 'showDemoOnly'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        if ($redirect = $this->ensureCelebrationPack()) {
            return $redirect;
        }

        $validated = $request->validate([
            'wedding_date' => ['nullable', 'date'],
            'custom_text' => ['nullable', 'string', 'max:255'],
        ]);

        $data = [];
        if ($request->has('wedding_date')) {
            $data['wedding_date'] = $request->input('wedding_date') ?: null;
        }
        if ($request->has('custom_text')) {
            $data['custom_text'] = $request->input('custom_text') ?: null;
        }

        if (!empty($data)) {
            PreWeddingSetting::updateOrCreate(
                ['user_id' => Auth::user()->id],
                $data
            );
        }

        return back()->with('success', 'Pre-wedding settings saved.');
    }

    public function upload(Request $request): RedirectResponse
    {
        if ($redirect = $this->ensureCelebrationPack()) {
            return $redirect;
        }

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:15360'],
        ]);

        $userId = Auth::user()->id;
        $disk = Storage::disk('public');
        $path = $request->file('photo')->store("pre_wedding/{$userId}", 'public');

        // Delete existing assets files to save space
        $oldAssets = PreWeddingAsset::where('user_id', $userId)->get();
        foreach ($oldAssets as $oldAsset) {
            if ($oldAsset->image_path && $disk->exists($oldAsset->image_path)) {
                $disk->delete($oldAsset->image_path);
            }
        }
        PreWeddingAsset::where('user_id', $userId)->delete();

        // Create new asset rows for all config milestones
        $milestones = config('pre_wedding.milestones', []);
        foreach (array_keys($milestones) as $key) {
            PreWeddingAsset::create([
                'user_id' => $userId,
                'milestone_key' => $key,
                'image_path' => $path,
            ]);
        }

        return back()->with('success', 'Pre-wedding photo saved and applied to all cards.');
    }

    public function card(Request $request, ?string $milestoneKey = null): Response|RedirectResponse
    {
        return $this->renderCard($request, false, $milestoneKey);
    }

    public function exportPng(Request $request, ?string $milestoneKey = null): Response|RedirectResponse
    {
        return $this->renderCard($request, true, $milestoneKey);
    }

    private function renderCard(Request $request, bool $pngExport, ?string $milestoneFromRoute = null): Response|RedirectResponse
    {
        if ($redirect = $this->ensureCelebrationPack()) {
            return $redirect;
        }

        $milestones = config('pre_wedding.milestones', []);
        $milestoneKey = $this->normalizeMilestoneKey($milestoneFromRoute, $request);

        if ($milestones === []) {
            if ($request->expectsJson()) {
                abort(503, 'Pre-wedding configuration is unavailable. Run php artisan config:clear on the server.');
            }

            return redirect()
                ->route('client.pre-wedding.index')
                ->withErrors(['milestone' => 'Pre-wedding data is not loaded. Ask your host to run php artisan config:clear (stale config cache).']);
        }

        if ($milestoneKey === null || ! isset($milestones[$milestoneKey])) {
            if ($request->expectsJson()) {
                abort(422, 'Invalid or missing milestone. Use a key like 365, 100, …, 1, or marriage_day.');
            }

            return redirect()
                ->route('client.pre-wedding.index')
                ->withErrors(['milestone' => 'Invalid countdown. Open Pre-wedding and use Preview / Download from the list — or open /client/pre-wedding/card/365 (with your number in the path).']);
        }
        $userId = Auth::user()->id;
        if (Auth::user()->isFamilyMember() && !\App\Models\PreWeddingAsset::where('user_id', $userId)->exists() && !\App\Models\PreWeddingSetting::where('user_id', $userId)->exists()) {
            $userId = Auth::user()->parent_user_id;
        }
        $asset = PreWeddingAsset::where('user_id', $userId)
            ->where('milestone_key', $milestoneKey)
            ->first();

        $meta = config("pre_wedding.milestones.{$milestoneKey}");
        $theme = $meta['theme'] ?? 'misty_dusk';

        $disk = Storage::disk('public');
        $path = $asset?->image_path;
        $path = is_string($path) ? ltrim(str_replace('\\', '/', $path), '/') : null;
        if ($path && str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        $hasFile = $path && $disk->exists($path);
        $publicUrl = $hasFile ? $disk->url($path) : null;

        if ($pngExport) {
            $dataUri = $this->publicImageAsDataUri($path);
            $bgUrl = $dataUri ?? $publicUrl ?? $this->defaultPhotoUrl();
        } else {
            $bgUrl = $publicUrl ?? $this->defaultPhotoUrl();
        }

        $pngExportFilename = $this->pngFilename($milestoneKey);

        $setting = PreWeddingSetting::where('user_id', $userId)->first();
        $customText = $setting?->custom_text;

        return response()
            ->view('client.pre-wedding.card-export', [
                'milestoneKey' => $milestoneKey,
                'theme' => $theme,
                'headline' => $meta['headline'] ?? '',
                'headlineSmall' => $meta['headline_small'] ?? null,
                'subline' => $meta['subline'] ?? null,
                'quote' => $meta['quote'] ?? '',
                'bgUrl' => $bgUrl,
                'pngExportScript' => $pngExport,
                'pngExportFilename' => $pngExportFilename,
                'customText' => $customText,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    private function pngFilename(string $milestoneKey): string
    {
        $safe = preg_replace('/[^a-z0-9_-]+/i', '-', $milestoneKey);

        return 'pre-wedding-'.($safe ?: 'card').'.png';
    }

    private function defaultPhotoUrl(): string
    {
        return asset('images/prewedding-default.jpg');
    }

    private function fallbackGradientDataUri(): string
    {
        $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="810" height="1440" viewBox="0 0 810 1440"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#1e293b"/><stop offset="100%" style="stop-color:#312e81"/></linearGradient></defs><rect width="810" height="1440" fill="url(#g)"/></svg>
SVG;

        return 'data:image/svg+xml,'.rawurlencode($svg);
    }

    private function publicImageAsDataUri(?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }
        $full = storage_path('app/public/'.$relativePath);
        if (! is_readable($full)) {
            return null;
        }
        $binary = @file_get_contents($full);
        if ($binary === false || $binary === '') {
            return null;
        }
        $mime = @mime_content_type($full);
        if (! is_string($mime) || ! str_starts_with($mime, 'image/')) {
            $mime = 'image/jpeg';
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }

    private function normalizeMilestoneKey(?string $fromRoute, Request $request): ?string
    {
        $raw = $fromRoute;
        if ($raw === null || $raw === '') {
            $q = $request->query('milestone');
            $raw = is_scalar($q) ? (string) $q : '';
        }
        $raw = trim($raw);

        return $raw === '' ? null : $raw;
    }

    public function thumbnailPreview(Request $request, string $milestoneKey): Response
    {
        $milestones = config('pre_wedding.milestones', []);
        if (!isset($milestones[$milestoneKey])) {
            abort(404);
        }

        $userId = Auth::user()->dataOwnerId();
        $asset = PreWeddingAsset::where('user_id', $userId)
            ->where('milestone_key', $milestoneKey)
            ->first();

        $meta = $milestones[$milestoneKey];
        $theme = $meta['theme'] ?? 'misty_dusk';

        $disk = Storage::disk('public');
        $path = $asset?->image_path;
        $path = is_string($path) ? ltrim(str_replace('\\', '/', $path), '/') : null;
        if ($path && str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        $hasFile = $path && $disk->exists($path);
        $publicUrl = $hasFile ? $disk->url($path) : null;
        $bgUrl = $publicUrl ?? $this->defaultPhotoUrl();

        $setting = PreWeddingSetting::where('user_id', $userId)->first();
        $customText = $setting?->custom_text;

        return response()
            ->view('client.pre-wedding.card-export', [
                'milestoneKey' => $milestoneKey,
                'theme' => $theme,
                'headline' => $meta['headline'] ?? '',
                'headlineSmall' => $meta['headline_small'] ?? null,
                'subline' => $meta['subline'] ?? null,
                'quote' => $meta['quote'] ?? '',
                'bgUrl' => $bgUrl,
                'pngExportScript' => false,
                'pngExportFilename' => $this->pngFilename($milestoneKey),
                'demoThumbIframe' => true,
                'customText' => $customText,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    private function ensureCelebrationPack(): ?RedirectResponse
    {
        if (Auth::user()->hasCelebrationPackAccess()) {
            return null;
        }

        if (request()->expectsJson()) {
            abort(403, 'Pay the Celebration Plan to use pre-wedding uploads and exports.');
        }

        return redirect()
            ->route('client.pre-wedding.index')
            ->withErrors(['pack' => 'Pay the Celebration Plan to upload photos, preview cards, and download PNGs.']);
    }
}
