@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Users
    </a>
    <h1 class="text-3xl font-bold text-gray-800">User Details</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center space-x-6 mb-6">
                <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-indigo-600 font-bold text-2xl">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <p class="text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subscription</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->subscription_status === 'premium' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($user->subscription_status ?? 'free') }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Joined</label>
                    <p class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-block">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </a>
            </div>
        </div>

        <!-- User Events -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Events ({{ $user->events->count() }})</h3>
            <div class="space-y-4">
                @forelse($user->events as $event)
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="font-semibold text-gray-900">{{ $event->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $event->event_date->format('M d, Y') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No events</p>
                @endforelse
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Statistics</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Total Events</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $user->events->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Contacts</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $user->contacts->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $user->upiTransactions->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
