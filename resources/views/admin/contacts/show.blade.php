@extends('layouts.admin')

@section('title', 'Contact Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.contacts.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Contacts
    </a>
    <h1 class="text-3xl font-bold text-gray-800">Contact Details</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <div class="flex items-center space-x-6 mb-6">
        <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center">
            <span class="text-indigo-600 font-bold text-2xl">{{ substr($contact->name, 0, 1) }}</span>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $contact->name }}</h2>
            @if($contact->is_favorite)
                <span class="inline-flex items-center text-yellow-500">
                    <i class="fas fa-star mr-1"></i>Favorite
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <p class="text-gray-900">{{ $contact->phone ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <p class="text-gray-900">{{ $contact->email ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <p class="text-gray-900">{{ $contact->address ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
            <p class="text-gray-900">{{ $contact->relationship ?? 'N/A' }}</p>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <p class="text-gray-900">{{ $contact->notes ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
            <p class="text-gray-900">{{ $contact->user->name }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
            <p class="text-gray-900">{{ $contact->created_at->format('M d, Y') }}</p>
        </div>
    </div>
</div>
@endsection
