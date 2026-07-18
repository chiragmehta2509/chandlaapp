@extends('layouts.admin')

@section('title', 'Event Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Events
    </a>
    <h1 class="text-3xl font-bold text-gray-800">{{ $event->title }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Event Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="text-gray-900 mt-1">{{ $event->description ?? 'N/A' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Event Date</label>
                        <p class="text-gray-900 mt-1">{{ $event->event_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Event Time</label>
                        <p class="text-gray-900 mt-1">{{ $event->event_time ? $event->event_time->format('h:i A') : 'N/A' }}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Venue</label>
                    <p class="text-gray-900 mt-1">{{ $event->venue ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Event Type</label>
                    <p class="text-gray-900 mt-1">{{ $event->event_type ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created By</label>
                    <p class="text-gray-900 mt-1">{{ $event->user->name }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Entries ({{ $event->entries->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($event->entries as $entry)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $entry->guest_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $entry->guest_phone }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-gray-500">No entries</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Statistics</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Total Entries</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $event->entries->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Invitations</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $event->invitations->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Collaborators</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $event->collaborators->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
