<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class MarriageInvitationCard
{
    /**
     * Card rendering / previews: fill blanks with demo_card_data; user-entered values always win.
     *
     * @param  array<string, mixed>|null  $userData
     * @return array<string, mixed>
     */
    public static function mergeUserDataWithDemoDefaults(?array $userData): array
    {
        $defaults = config('marriage_invitations.demo_card_data', []);
        $defaults = is_array($defaults) ? $defaults : [];
        $user = is_array($userData) ? $userData : [];

        $out = $defaults;

        foreach ($user as $key => $value) {
            if ($key === 'schedule_events') {
                if (! is_array($value)) {
                    continue;
                }
                if (self::scheduleEventsHaveTitles($value)) {
                    $out['schedule_events'] = $value;
                }

                continue;
            }

            if ($value === null) {
                continue;
            }

            if (is_string($value)) {
                if (trim($value) === '') {
                    continue;
                }
                $out[$key] = $value;

                continue;
            }

            if (is_array($value)) {
                if ($value === []) {
                    continue;
                }
                $out[$key] = $value;

                continue;
            }

            $out[$key] = $value;
        }

        $hasCoupleUpload = ! empty($user['couple_image'])
            && is_string($user['couple_image'])
            && trim($user['couple_image']) !== '';
        if ($hasCoupleUpload) {
            unset($out['demo_couple_image_url']);
        }

        return $out;
    }

    /**
     * @param  list<mixed>  $events
     */
    private static function scheduleEventsHaveTitles(array $events): bool
    {
        foreach ($events as $ev) {
            if (! is_array($ev)) {
                continue;
            }
            if (trim((string) ($ev['title'] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public static function templateKeys(): array
    {
        return array_keys(config('marriage_invitations.templates', []));
    }

    /**
     * @param  array<string, mixed>  $d
     * @return array<string, mixed>
     */
    public static function viewData(
        array $d,
        ?string $coupleImagePdfSrc,
        ?string $coupleImageDataUri,
        bool $pngExportScript
    ): array {
        $dateLine = '';
        if (!empty($d['wedding_date'])) {
            try {
                $dateLine = \Carbon\Carbon::parse($d['wedding_date'])->format('l, F j, Y');
            } catch (\Throwable) {
                $dateLine = (string) $d['wedding_date'];
            }
        }

        $demoUrl = '';
        if (!empty($d['demo_couple_image_url']) && is_string($d['demo_couple_image_url'])) {
            $demoUrl = trim($d['demo_couple_image_url']);
        }
        if ($demoUrl !== '' && str_starts_with(strtolower($demoUrl), 'https://')) {
            $imgSrc = $demoUrl;
            if ($pngExportScript && is_string($coupleImageDataUri) && $coupleImageDataUri !== '') {
                $imgSrc = $coupleImageDataUri;
            }

            return [
                'dateLine' => $dateLine,
                'couplePath' => null,
                'coupleImageOk' => true,
                'coupleImageUrl' => $demoUrl,
                'imgSrc' => $imgSrc,
            ];
        }

        $couplePath = !empty($d['couple_image']) && is_string($d['couple_image']) ? $d['couple_image'] : null;
        if ($couplePath) {
            $couplePath = ltrim(str_replace('\\', '/', $couplePath), '/');
            if (str_starts_with($couplePath, 'storage/')) {
                $couplePath = substr($couplePath, strlen('storage/'));
            }
        } else {
            $couplePath = null;
        }

        $disk = Storage::disk('public');
        $coupleImageOk = $couplePath && $disk->exists($couplePath);
        $coupleImageUrl = $coupleImageOk ? $disk->url($couplePath) : null;

        $imgSrc = ($coupleImagePdfSrc ?? null) ? $coupleImagePdfSrc : $coupleImageUrl;
        if ($pngExportScript && is_string($coupleImageDataUri) && $coupleImageDataUri !== '') {
            $imgSrc = $coupleImageDataUri;
        }

        return [
            'dateLine' => $dateLine,
            'couplePath' => $couplePath,
            'coupleImageOk' => $coupleImageOk,
            'coupleImageUrl' => $coupleImageUrl,
            'imgSrc' => $imgSrc,
        ];
    }

    /**
     * Format stored wedding time for invitation layouts (accepts HH:MM or legacy free text).
     */
    public static function formatWeddingTimeForDisplay(?string $t): string
    {
        $t = trim((string) $t);
        if ($t === '') {
            return '';
        }

        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $t)) {
            try {
                $fmt = strlen($t) >= 8 ? 'H:i:s' : 'H:i';

                return \Carbon\Carbon::createFromFormat($fmt, $t)->format('g:i A');
            } catch (\Throwable) {
                // fall through
            }
        }

        try {
            return \Carbon\Carbon::parse($t)->format('g:i A');
        } catch (\Throwable) {
            return $t;
        }
    }

    public static function normalizeLayoutKey(?string $layout, ?string $invitationDefault): string
    {
        $keys = self::templateKeys();
        if ($keys === []) {
            return 'heritage';
        }
        if ($layout !== null && in_array($layout, $keys, true)) {
            return $layout;
        }
        if ($invitationDefault !== null && in_array($invitationDefault, $keys, true)) {
            return $invitationDefault;
        }

        return $keys[0];
    }
}
