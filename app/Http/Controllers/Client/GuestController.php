<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::whereIn('user_id', Auth::user()->allowedUserIds());

        if ($request->favorites) {
            $query->where('is_favorite', true);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%')
                  ->orWhere('relationship', 'like', '%' . $request->search . '%');
            });
        }

        $allowedPerPage = [10, 50, 100];
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $guests = $query->orderBy('name', 'asc')->paginate($perPage)->withQueryString();

        return view('client.guests.index', compact('guests', 'perPage'));
    }

    public function create()
    {
        return view('client.guests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:60',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'city'         => 'required|string|max:100',
            'relationship' => 'required|string|max:100',
            'notes'        => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::user()->id;
        $guest = Guest::create($validated);

        return redirect()->route('client.guests.show', $guest->id)->with('success', 'Guest added successfully');
    }

    public function show($id)
    {
        $guest = Guest::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        return view('client.guests.show', compact('guest'));
    }

    public function edit($id)
    {
        $guest = Guest::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        return view('client.guests.edit', compact('guest'));
    }

    public function update(Request $request, $id)
    {
        $guest = Guest::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:60',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'city'         => 'required|string|max:100',
            'relationship' => 'required|string|max:100',
            'notes'        => 'nullable|string',
        ]);

        $guest->update($validated);

        return redirect()->route('client.guests.show', $guest->id)->with('success', 'Guest updated successfully');
    }

    public function destroy($id)
    {
        $guest = Guest::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        // Family members cannot delete guests that belong to the parent.
        if (Auth::user()->isFamilyMember() && (int) $guest->user_id !== (int) Auth::user()->id) {
            abort(403, 'Family members cannot delete parent guests.');
        }

        $guest->delete();

        return redirect()->route('client.guests.index')->with('success', 'Guest deleted successfully');
    }

    public function toggleFavorite($id)
    {
        $guest = Guest::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $guest->update(['is_favorite' => !$guest->is_favorite]);

        return back()->with('success', 'Favorite status updated');
    }

    public function importForm()
    {
        return view('client.guests.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'guests_file' => 'required|file|mimes:vcf,vcard,txt,csv|max:5120',
        ], [
            'guests_file.mimes' => 'Upload a .vcf (vCard) or .csv file exported from your phone.',
            'guests_file.max'   => 'File must be 5 MB or smaller.',
        ]);

        $file     = $request->file('guests_file');
        $contents = file_get_contents($file->getRealPath());
        if ($contents === false || trim($contents) === '') {
            return back()->withErrors(['guests_file' => 'Could not read the uploaded file.']);
        }

        $ext  = strtolower($file->getClientOriginalExtension());
        $rows = $ext === 'csv'
            ? $this->parseCsvGuests($contents)
            : $this->parseVcfGuests($contents);

        if (empty($rows)) {
            return back()->withErrors(['guests_file' => 'No guests could be read from the file.']);
        }

        $userId         = Auth::user()->id;
        $existing       = Guest::whereIn('user_id', Auth::user()->allowedUserIds())->select('phone', 'email')->get();
        $existingPhones = $existing->pluck('phone')
            ->filter(fn ($p) => trim((string) $p) !== '')
            ->map(fn ($p) => $this->normalizePhone($p))
            ->flip();
        $existingEmails = $existing->pluck('email')
            ->filter(fn ($e) => trim((string) $e) !== '')
            ->map(fn ($e) => strtolower(trim($e)))
            ->flip();

        $imported = 0;
        $skipped  = 0;
        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') { $skipped++; continue; }

            $phones       = $row['phones'] ?? [];
            $emails       = $row['emails'] ?? [];
            $primaryPhone = $phones[0]['number'] ?? ($row['phone'] ?? '');
            $primaryEmail = $emails[0]['address'] ?? ($row['email'] ?? '');

            $phoneKey = $primaryPhone !== '' ? $this->normalizePhone($primaryPhone) : null;
            $emailKey = $primaryEmail !== '' ? strtolower($primaryEmail) : null;

            if (($phoneKey !== null && isset($existingPhones[$phoneKey])) ||
                ($emailKey !== null && isset($existingEmails[$emailKey]))) {
                $skipped++;
                continue;
            }

            Guest::create([
                'user_id'      => $userId,
                'name'         => mb_substr($name, 0, 255),
                'phone'        => $primaryPhone !== '' ? mb_substr($primaryPhone, 0, 60) : null,
                'phones'       => !empty($phones) ? $phones : null,
                'email'        => $primaryEmail !== '' && filter_var($primaryEmail, FILTER_VALIDATE_EMAIL) ? $primaryEmail : null,
                'emails'       => !empty($emails) ? $emails : null,
                'address'      => trim((string) ($row['address'] ?? '')) ?: null,
                'notes'        => trim((string) ($row['notes'] ?? '')) ?: null,
            ]);

            if ($phoneKey !== null) $existingPhones[$phoneKey] = true;
            if ($emailKey !== null) $existingEmails[$emailKey] = true;
            $imported++;
        }

        $msg = "Imported {$imported} guest" . ($imported === 1 ? '' : 's') . '.';
        if ($skipped > 0) {
            $msg .= " Skipped {$skipped} (no name or already in your guests).";
        }

        return redirect()->route('client.guests.index')->with('success', $msg);
    }

    // ─── vCard Parser ────────────────────────────────────────────────────────
    private function parseVcfGuests(string $contents): array
    {
        $contents = preg_replace("/\r\n[ \t]/", '', $contents);
        $contents = preg_replace("/\n[ \t]/", '', $contents);
        $contents = str_replace("\r\n", "\n", $contents);

        $rows   = [];
        $blocks = preg_split('/BEGIN:VCARD/i', $contents);

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') continue;
            $endPos = stripos($block, 'END:VCARD');
            if ($endPos !== false) $block = substr($block, 0, $endPos);

            $row = [
                'name' => '', 'phones' => [], 'emails' => [],
                'address' => '', 'notes' => '',
            ];
            $nameFromN = '';

            foreach (preg_split("/\n/", $block) as $line) {
                $line  = trim($line);
                if ($line === '') continue;
                $colon = strpos($line, ':');
                if ($colon === false) continue;
                $head  = substr($line, 0, $colon);
                $value = substr($line, $colon + 1);

                $params     = explode(';', $head);
                $prop       = strtoupper(array_shift($params));
                $isQp       = false;
                $charset    = null;
                $typeLabels = [];

                foreach ($params as $p) {
                    $pUp = strtoupper($p);
                    if ($pUp === 'QUOTED-PRINTABLE' || str_contains($pUp, 'QUOTED-PRINTABLE')) $isQp = true;
                    if (str_starts_with($pUp, 'CHARSET=')) $charset = substr($p, 8);
                    if (str_starts_with($pUp, 'TYPE=')) {
                        foreach (explode(',', substr($p, 5)) as $t) {
                            $t = trim(strtolower($t));
                            if ($t !== '' && $t !== 'pref' && $t !== 'voice') $typeLabels[] = $t;
                        }
                    } elseif (!str_contains($pUp, '=')) {
                        $bare = trim(strtolower($p));
                        if ($bare !== '' && $bare !== 'pref' && $bare !== 'voice') $typeLabels[] = $bare;
                    }
                }

                if ($isQp) {
                    $value = quoted_printable_decode($value);
                    if ($charset && strtoupper($charset) !== 'UTF-8') {
                        $value = @mb_convert_encoding($value, 'UTF-8', $charset) ?: $value;
                    }
                }

                $value = str_replace(['\\n', '\\N'], "\n", $value);
                $value = str_replace(['\\,', '\\;'], [',', ';'], $value);
                $label = !empty($typeLabels) ? ucfirst(implode('/', $typeLabels)) : 'Mobile';

                switch ($prop) {
                    case 'FN':
                        if ($row['name'] === '') $row['name'] = trim($value);
                        break;
                    case 'N':
                        if ($nameFromN === '') {
                            $parts     = explode(';', $value);
                            $given     = trim($parts[1] ?? '');
                            $family    = trim($parts[0] ?? '');
                            $middle    = trim($parts[2] ?? '');
                            $nameFromN = trim(implode(' ', array_filter([$given, $middle, $family])));
                        }
                        break;
                    case 'TEL':
                        $num = trim($value);
                        if ($num !== '') $row['phones'][] = ['label' => $label, 'number' => $num];
                        break;
                    case 'EMAIL':
                        $addr = trim($value);
                        if ($addr !== '') $row['emails'][] = ['label' => $label, 'address' => $addr];
                        break;
                    case 'ADR':
                        if ($row['address'] === '') {
                            $parts = explode(';', $value);
                            $row['address'] = trim(implode(', ', array_filter(array_map('trim', $parts))));
                        }
                        break;
                    case 'NOTE':
                        if ($row['notes'] === '') $row['notes'] = trim($value);
                        break;
                }
            }

            if ($row['name'] === '' && $nameFromN !== '') $row['name'] = $nameFromN;
            $row['phone'] = $row['phones'][0]['number'] ?? '';
            $row['email'] = $row['emails'][0]['address'] ?? '';

            if ($row['name'] !== '' || $row['phone'] !== '' || $row['email'] !== '') {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    // ─── CSV Parser ───────────────────────────────────────────────────────────
    private function parseCsvGuests(string $contents): array
    {
        $lines  = [];
        $stream = fopen("php://temp", "r+");
        fwrite($stream, $contents);
        rewind($stream);
        while (($data = fgetcsv($stream)) !== false) {
            if (empty(array_filter($data, fn ($col) => trim((string) $col) !== ''))) continue;
            $lines[] = $data;
        }
        fclose($stream);

        if (count($lines) < 2) return [];

        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($lines));

        $singleAliases = [
            'name'    => ['name', 'full name', 'display name', 'first name', 'given name', 'contact name'],
            'email'   => ['email', 'e-mail', 'email address', 'e-mail address', 'email 1 - value', 'primary email'],
            'address' => ['address', 'home address', 'street', 'address 1 - formatted'],
            'notes'   => ['notes', 'note'],
        ];
        $idx = [];
        foreach ($singleAliases as $key => $names) {
            foreach ($names as $alias) {
                $found = array_search($alias, $headers, true);
                if ($found !== false) { $idx[$key] = $found; break; }
            }
        }

        $phoneColumns = [];
        foreach ($headers as $i => $h) {
            if (preg_match('/^phone\s*\d*\s*-\s*value$/i', $h) || $h === 'phone' || $h === 'mobile' || $h === 'cell' || $h === 'telephone') {
                $typeIdx = null;
                $base    = preg_replace('/-\s*value$/i', '- type', $h);
                $found   = array_search($base, $headers, true);
                if ($found !== false) $typeIdx = $found;
                $phoneColumns[] = ['val' => $i, 'type' => $typeIdx];
            }
        }

        $emailColumns = [];
        foreach ($headers as $i => $h) {
            if (preg_match('/^email\s*\d*\s*-\s*value$/i', $h) || $h === 'email' || $h === 'e-mail') {
                $typeIdx = null;
                $base    = preg_replace('/-\s*value$/i', '- type', $h);
                $found   = array_search($base, $headers, true);
                if ($found !== false) $typeIdx = $found;
                $emailColumns[] = ['val' => $i, 'type' => $typeIdx];
            }
        }

        $rows = [];
        foreach ($lines as $cols) {
            $row = ['phones' => [], 'emails' => []];

            foreach ($idx as $key => $i) {
                $row[$key] = isset($cols[$i]) ? trim((string) $cols[$i]) : '';
            }

            foreach ($phoneColumns as $pc) {
                $num = isset($cols[$pc['val']]) ? trim($cols[$pc['val']]) : '';
                if ($num === '') continue;
                $label = ($pc['type'] !== null && isset($cols[$pc['type']])) ? trim($cols[$pc['type']]) : 'Mobile';
                if ($label === '') $label = 'Mobile';
                $row['phones'][] = ['label' => $label, 'number' => $num];
            }

            foreach ($emailColumns as $ec) {
                $addr = isset($cols[$ec['val']]) ? trim($cols[$ec['val']]) : '';
                if ($addr === '') continue;
                $label = ($ec['type'] !== null && isset($cols[$ec['type']])) ? trim($cols[$ec['type']]) : 'Home';
                if ($label === '') $label = 'Home';
                $row['emails'][] = ['label' => $label, 'address' => $addr];
            }

            $row['phone'] = $row['phones'][0]['number'] ?? '';
            $row['email'] = $row['emails'][0]['address'] ?? '';

            if (!empty($row['name']) || !empty($row['phone']) || !empty($row['email'])) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?: $phone;
    }
}
