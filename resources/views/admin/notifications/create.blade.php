@extends('layouts.admin')

@section('title', 'Send Push Notifications')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Send Push Notifications</h1>
            <p class="text-gray-600 mt-1">Broadcast messages to all users or target specific plans.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">Compose Notification</h2>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Title -->
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Special Offer!" value="{{ old('title') }}" required>
                </div>
                
                <!-- Message -->
                <div class="col-span-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" id="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter notification message here..." required>{{ old('message') }}</textarea>
                </div>

                <!-- Target Audience -->
                <div class="col-span-1">
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-1">Target Audience <span class="text-red-500">*</span></label>
                    <select name="target_audience" id="target_audience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>All Active Users</option>
                        <option value="plan_wise" {{ old('target_audience') == 'plan_wise' ? 'selected' : '' }}>Plan Wise</option>
                        <option value="specific_users" {{ old('target_audience') == 'specific_users' ? 'selected' : '' }}>Specific Users</option>
                    </select>
                </div>

                <!-- Select Plan -->
                <div class="col-span-1" id="plan_level_container" style="display: none;">
                    <label for="plan_level" class="block text-sm font-medium text-gray-700 mb-1">Select Plan <span class="text-red-500">*</span></label>
                    <select name="plan_level" id="plan_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="0" {{ old('plan_level') == '0' ? 'selected' : '' }}>Free</option>
                        <option value="1" {{ old('plan_level') == '1' ? 'selected' : '' }}>Celebration (₹300)</option>
                        <option value="2" {{ old('plan_level') == '2' ? 'selected' : '' }}>Guest Contribution (₹400)</option>
                        <option value="3" {{ old('plan_level') == '3' ? 'selected' : '' }}>Host Plus / Ledger Duo (₹500)</option>
                        <option value="4" {{ old('plan_level') == '4' ? 'selected' : '' }}>Family Plan (₹600)</option>
                        <option value="5" {{ old('plan_level') == '5' ? 'selected' : '' }}>Premium Host (₹700)</option>
                        <option value="6" {{ old('plan_level') == '6' ? 'selected' : '' }}>Professional (₹999)</option>
                        <option value="7" {{ old('plan_level') == '7' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Users whose highest active plan matches this selection.</p>
                </div>
                
                <!-- Specific Users -->
                <div class="col-span-2" id="specific_users_container" style="display: none;">
                    <label for="specific_user_ids" class="block text-sm font-medium text-gray-700 mb-1">Select Users <span class="text-red-500">*</span></label>
                    <select name="specific_user_ids[]" id="specific_user_ids" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" multiple style="height: 200px;">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, old('specific_user_ids', [])) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->phone }}) - {{ $user->has_token ? '✅ Has Token' : '❌ No Token' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Command (Mac) to select multiple users. Users marked with ❌ will not receive push notifications even if selected.</p>
                </div>
                
                <!-- All Users Table -->
                <div class="col-span-2" id="all_users_container" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">All Active Users Overview</label>
                    <div class="overflow-y-auto border border-gray-300 rounded-lg max-h-64">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($user->has_token)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">✅ Has Token</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">❌ No Token</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Users marked with ❌ will automatically be skipped during broadcast.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium flex items-center" id="sendBtn">
                    <i class="fas fa-paper-plane mr-2"></i> Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetAudience = document.getElementById('target_audience');
        const planLevelContainer = document.getElementById('plan_level_container');
        const planLevelSelect = document.getElementById('plan_level');
        const specificUsersContainer = document.getElementById('specific_users_container');
        const specificUsersSelect = document.getElementById('specific_user_ids');
        const allUsersContainer = document.getElementById('all_users_container');
        const sendBtn = document.getElementById('sendBtn');
        const form = sendBtn.closest('form');

        function toggleVisibility() {
            if (targetAudience.value === 'plan_wise') {
                planLevelContainer.style.display = 'block';
                planLevelSelect.required = true;
                specificUsersContainer.style.display = 'none';
                specificUsersSelect.required = false;
                allUsersContainer.style.display = 'none';
            } else if (targetAudience.value === 'specific_users') {
                planLevelContainer.style.display = 'none';
                planLevelSelect.required = false;
                specificUsersContainer.style.display = 'block';
                specificUsersSelect.required = true;
                allUsersContainer.style.display = 'none';
            } else {
                planLevelContainer.style.display = 'none';
                planLevelSelect.required = false;
                specificUsersContainer.style.display = 'none';
                specificUsersSelect.required = false;
                allUsersContainer.style.display = 'block';
            }
        }

        targetAudience.addEventListener('change', toggleVisibility);
        
        // Initial check in case of old input
        toggleVisibility();

        form.addEventListener('submit', function() {
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
            sendBtn.disabled = true;
            sendBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });
    });
</script>
@endpush
@endsection
