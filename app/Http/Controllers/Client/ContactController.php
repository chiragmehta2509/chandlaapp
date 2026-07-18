<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::whereIn('user_id', Auth::user()->allowedUserIds());

        if ($request->favorites) {
            $query->where('is_favorite', true);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $allowedPerPage = [10, 50, 100];
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $contacts = $query->orderBy('name', 'asc')->paginate($perPage)->withQueryString();

        return view('client.contacts.index', compact('contacts', 'perPage'));
    }

    public function create()
    {
        return view('client.contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|required_without:email',
            'email' => 'nullable|email|required_without:phone',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'phone.required_without' => 'Please provide either a phone number or an email address.',
            'email.required_without' => 'Please provide either a phone number or an email address.',
        ]);

        $validated['user_id'] = Auth::user()->id;
        $contact = Contact::create($validated);

        return redirect()->route('client.contacts.show', $contact->id)->with('success', 'Contact created successfully');
    }

    public function show($id)
    {
        $contact = Contact::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        return view('client.contacts.show', compact('contact'));
    }

    public function edit($id)
    {
        $contact = Contact::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        return view('client.contacts.edit', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|required_without:email',
            'email' => 'nullable|email|required_without:phone',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'phone.required_without' => 'Please provide either a phone number or an email address.',
            'email.required_without' => 'Please provide either a phone number or an email address.',
        ]);

        $contact->update($validated);

        return redirect()->route('client.contacts.show', $contact->id)->with('success', 'Contact updated successfully');
    }

    public function destroy($id)
    {
        $contact = Contact::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        // Family members cannot delete contacts that belong to the parent.
        if (Auth::user()->isFamilyMember() && (int) $contact->user_id !== (int) Auth::user()->id) {
            abort(403, 'Family members cannot delete parent contacts.');
        }

        $contact->delete();

        return redirect()->route('client.contacts.index')->with('success', 'Contact deleted successfully');
    }

    public function toggleFavorite($id)
    {
        $contact = Contact::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $contact->update(['is_favorite' => !$contact->is_favorite]);

        return back()->with('success', 'Favorite status updated');
    }

    public function importForm()
    {
        return view('client.contacts.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'contacts_file' => 'required|file|mimes:vcf,vcard,txt,csv|max:5120',
        ], [
            'contacts_file.mimes' => 'Upload a .vcf (vCard) or .csv file exported from your phone.',
            'contacts_file.max'   => 'File must be 5 MB or smaller.',
        ]);

        $file     = $request->file('contacts_file');
        $contents = file_get_contents($file->getRealPath());
        if ($contents === false || trim($contents) === '') {
            return back()->withErrors(['contacts_file' => 'Could not read the uploaded file.']);
        }

        $ext  = strtolower($file->getClientOriginalExtension());
        $rows = $ext === 'csv'
            ? $this->parseCsvContacts($contents)
            : $this->parseVcfContacts($contents);

        if (empty($rows)) {
            return back()->withErrors(['contacts_file' => 'No contacts could be read from the file.']);
        }

        $userId        = Auth::user()->id;
        $existing      = Contact::whereIn('user_id', Auth::user()->allowedUserIds())->select('phone', 'email')->get();
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

            // Primary phone / email (first in list)
            $phones = $row['phones'] ?? [];
            $emails = $row['emails'] ?? [];
            $primaryPhone = $phones[0]['number'] ?? ($row['phone'] ?? '');
            $primaryEmail = $emails[0]['address'] ?? ($row['email'] ?? '');

            if (empty($primaryPhone) && empty($primaryEmail)) {
                $skipped++;
                continue;
            }

            $phoneKey = $primaryPhone !== '' ? $this->normalizePhone($primaryPhone) : null;
            $emailKey = $primaryEmail !== '' ? strtolower($primaryEmail) : null;

            if (($phoneKey !== null && isset($existingPhones[$phoneKey])) ||
                ($emailKey !== null && isset($existingEmails[$emailKey]))) {
                $skipped++;
                continue;
            }

            $birthday = null;
            if (!empty($row['birthday'])) {
                try { $birthday = \Carbon\Carbon::parse($row['birthday'])->toDateString(); } catch (\Throwable $e) {}
            }

            Contact::create([
                'user_id'      => $userId,
                'name'         => mb_substr($name, 0, 255),
                'phone'        => $primaryPhone !== '' ? mb_substr($primaryPhone, 0, 60) : null,
                'phones'       => !empty($phones) ? $phones : null,
                'email'        => $primaryEmail !== '' && filter_var($primaryEmail, FILTER_VALIDATE_EMAIL) ? $primaryEmail : null,
                'emails'       => !empty($emails) ? $emails : null,
                'address'      => trim((string) ($row['address'] ?? '')) ?: null,
                'organization' => trim((string) ($row['organization'] ?? '')) ?: null,
                'title'        => trim((string) ($row['title'] ?? '')) ?: null,
                'birthday'     => $birthday,
                'website'      => trim((string) ($row['website'] ?? '')) ?: null,
                'notes'        => trim((string) ($row['notes'] ?? '')) ?: null,
            ]);

            if ($phoneKey !== null) $existingPhones[$phoneKey] = true;
            if ($emailKey !== null) $existingEmails[$emailKey] = true;
            $imported++;
        }

        $msg = "Imported {$imported} contact" . ($imported === 1 ? '' : 's') . '.';
        if ($skipped > 0) {
            $msg .= " Skipped {$skipped} (missing name or already in your contacts).";
        }

        return redirect()->route('client.contacts.index')->with('success', $msg);
    }

    /**
     * Parse a vCard (.vcf) file into rows, collecting ALL phone/email entries with type labels.
     * Supports: multiple TEL, multiple EMAIL, ADR, ORG, TITLE, BDAY, URL, NOTE, FN/N.
     */
    private function parseVcfContacts(string $contents): array
    {
        // Unfold long lines (RFC 6350)
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
                'address' => '', 'organization' => '', 'title' => '',
                'birthday' => '', 'website' => '', 'notes' => '',
            ];
            $nameFromN = '';

            foreach (preg_split("/\n/", $block) as $line) {
                $line   = trim($line);
                if ($line === '') continue;
                $colon  = strpos($line, ':');
                if ($colon === false) continue;
                $head   = substr($line, 0, $colon);
                $value  = substr($line, $colon + 1);

                $params  = explode(';', $head);
                $prop    = strtoupper(array_shift($params));
                $isQp    = false;
                $charset = null;
                $typeLabels = [];

                foreach ($params as $p) {
                    $pUp = strtoupper($p);
                    if ($pUp === 'QUOTED-PRINTABLE' || str_contains($pUp, 'QUOTED-PRINTABLE')) $isQp = true;
                    if (str_starts_with($pUp, 'CHARSET=')) $charset = substr($p, 8);
                    // Collect TYPE= labels (TYPE=MOBILE, TYPE=HOME, TYPE=WORK etc.)
                    if (str_starts_with($pUp, 'TYPE=')) {
                        foreach (explode(',', substr($p, 5)) as $t) {
                            $t = trim(strtolower($t));
                            if ($t !== '' && $t !== 'pref' && $t !== 'voice') $typeLabels[] = $t;
                        }
                    } elseif (!str_contains($pUp, '=')) {
                        // Bare TYPE value e.g. ;CELL or ;HOME
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
                    case 'ORG':
                        if ($row['organization'] === '') {
                            $parts = explode(';', $value);
                            $row['organization'] = trim($parts[0]);
                        }
                        break;
                    case 'TITLE':
                        if ($row['title'] === '') $row['title'] = trim($value);
                        break;
                    case 'BDAY':
                        if ($row['birthday'] === '') $row['birthday'] = trim($value);
                        break;
                    case 'URL':
                        if ($row['website'] === '') $row['website'] = trim($value);
                        break;
                    case 'NOTE':
                        if ($row['notes'] === '') $row['notes'] = trim($value);
                        break;
                }
            }

            if ($row['name'] === '' && $nameFromN !== '') $row['name'] = $nameFromN;

            // Set primary phone/email for backward compat
            $row['phone'] = $row['phones'][0]['number'] ?? '';
            $row['email'] = $row['emails'][0]['address'] ?? '';

            if ($row['name'] !== '' || $row['phone'] !== '' || $row['email'] !== '') {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Parse a CSV with header row. Detects common column aliases.
     * Supports Google Contacts style multi-phone columns (Phone 1 - Value, Phone 2 - Value …).
     */
    private function parseCsvContacts(string $contents): array
    {
        $lines = [];
        $stream = fopen("php://temp", "r+");
        fwrite($stream, $contents);
        rewind($stream);
        while (($data = fgetcsv($stream)) !== false) {
            if (empty(array_filter($data, fn($col) => trim((string)$col) !== ''))) continue;
            $lines[] = $data;
        }
        fclose($stream);

        if (count($lines) < 2) return [];

        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($lines));

        // Map basic fields
        $singleAliases = [
            'name'         => ['name', 'full name', 'display name', 'first name', 'given name', 'contact name'],
            'email'        => ['email', 'e-mail', 'email address', 'e-mail address', 'email 1 - value', 'primary email'],
            'address'      => ['address', 'home address', 'street', 'address 1 - formatted'],
            'organization' => ['company', 'organization', 'organisation', 'org'],
            'title'        => ['title', 'job title', 'position'],
            'birthday'     => ['birthday', 'bday', 'date of birth', 'dob'],
            'website'      => ['website', 'url', 'web', 'homepage'],
            'notes'        => ['notes', 'note'],
        ];
        $idx = [];
        foreach ($singleAliases as $key => $names) {
            foreach ($names as $alias) {
                $found = array_search($alias, $headers, true);
                if ($found !== false) { $idx[$key] = $found; break; }
            }
        }

        // Collect all phone columns (Phone 1 - Value, Phone 2 - Value …)
        $phoneColumns = []; // [ [valueIdx, typeIdx], … ]
        foreach ($headers as $i => $h) {
            if (preg_match('/^phone\s*\d*\s*-\s*value$/i', $h) || $h === 'phone' || $h === 'mobile' || $h === 'cell' || $h === 'telephone') {
                $typeIdx = null;
                $base    = preg_replace('/-\s*value$/i', '- type', $h);
                $found   = array_search($base, $headers, true);
                if ($found !== false) $typeIdx = $found;
                $phoneColumns[] = ['val' => $i, 'type' => $typeIdx];
            }
        }

        // Collect all email columns
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
            $row  = ['phones' => [], 'emails' => []];

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

            $row['phone'] = $row['phones'][0]['number'] ?? ($row['email'] ?? '');
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
