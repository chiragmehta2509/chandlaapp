@extends('layouts.admin')

@section('title', 'Event Types Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Event Types Management</h1>
        <p class="text-gray-600 mt-1">Manage event types for the system</p>
    </div>
    <a href="{{ route('admin.event-types.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i>Add Event Type
    </a>
</div>

<!-- Event Types Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Events Count</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($eventTypes as $eventType)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($eventType->color)
                                    <div class="h-4 w-4 rounded-full mr-3" style="background-color: {{ $eventType->color }}"></div>
                                @endif
                                <div class="text-sm font-medium text-gray-900">{{ $eventType->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($eventType->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($eventType->icon)
                                <i class="{{ $eventType->icon }} text-gray-600"></i>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $eventType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $eventType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $eventType->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $eventType->events->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.event-types.edit', $eventType->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.event-types.destroy', $eventType->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will not delete events using this type.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No event types found. Create your first event type!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
