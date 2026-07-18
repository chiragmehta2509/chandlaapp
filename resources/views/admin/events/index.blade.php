@extends('layouts.admin')

@section('title', 'Events Management')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Events Management</h1>
    <p class="text-gray-600 mt-1">Manage all events in the system</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.events.index') }}" class="flex gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..." 
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Events</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-search mr-2"></i>Search
        </button>
    </form>
</div>

<!-- Events Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($events as $event)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $event->title }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($event->description, 100) }}</p>
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-calendar mr-2"></i>
                        {{ $event->event_date->format('M d, Y') }}
                    </div>
                    @if($event->venue)
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        {{ $event->venue }}
                    </div>
                    @endif
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-user mr-2"></i>
                        {{ $event->user->name }}
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $event->is_archived ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                        {{ $event->is_archived ? 'Archived' : 'Active' }}
                    </span>
                    <div class="space-x-2">
                        <a href="{{ route('admin.events.show', $event->id) }}" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-12">
            <p class="text-gray-500">No events found</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $events->links() }}
</div>
@endsection
