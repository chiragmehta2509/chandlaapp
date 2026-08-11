<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with('user');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $contacts = $query->orderBy('created_at', 'desc')->get();

        return view('admin.contacts.index', compact('contacts'));
    }

    public function show($id)
    {
        $contact = Contact::with('user')->findOrFail($id);
        return view('admin.contacts.show', compact('contact'));
    }
}
