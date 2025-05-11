<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
   
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .notification-highlight {
            animation: highlight 2s ease-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(59, 130, 246, 0.3); }
            100% { background-color: transparent; }
        }
        .unread-indicator::after {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #3B82F6;
            border-radius: 50%;
            margin-left: 8px;
        }
    </style>
</head>
<body class="main-content min-h-screen bg-gray-50">

    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 sm:p-8">
            <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-100 max-w-4xl mx-auto">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
                        <p class="text-gray-500 text-sm">Stay updated with your HR activities</p>
                    </div>
                    
                    <div class="flex space-x-2">
                        <form action="{{ route('admin.notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center text-sm bg-blue-50 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-100 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Mark All Read
                            </button>
                        </form>
                        
                        <button id="deleteSelectedBtn" class="flex items-center text-sm bg-red-50 text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>

                @if(Auth::user()->notifications->isEmpty() && empty($terminationNotifications))
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-700">No notifications yet</h3>
                        <p class="mt-1 text-gray-500">You'll see important updates here when they arrive.</p>
                    </div>
                @else
                    <!-- Filter Tabs -->
                    <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg mb-6">
                        <button onclick="filterNotifications('all')" class="flex-1 px-4 py-2 text-sm font-medium rounded-md focus:outline-none transition-all" id="allTab">
                            All Notifications
                        </button>
                        <button onclick="filterNotifications('unread')" class="flex-1 px-4 py-2 text-sm font-medium rounded-md focus:outline-none transition-all" id="unreadTab">
                            Unread Only
                        </button>
                    </div>

                    <!-- Notification List -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-3 border-b border-gray-200 flex items-center">
                            <input type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)" class="mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="selectAll" class="text-sm text-gray-700 cursor-pointer">Select all notifications</label>
                        </div>
                        
                        <div class="overflow-y-auto max-h-[500px] scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            <ul class="divide-y divide-gray-200">
                            @foreach($notifications as $notification)
    <li class="notification-item p-4 hover:bg-gray-50 transition-all {{ $notification->read_at ? '' : 'unread bg-blue-50 notification-highlight' }}" data-id="{{ $notification->id }}" data-read="{{ $notification->read_at ? 'true' : 'false' }}">
        <div class="flex items-start">
            <input type="checkbox" class="notification-checkbox mt-1 mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="{{ $notification->id }}">
            
            <div class="flex-shrink-0 mr-3">
    @if(isset($notification->data['profile_picture']) && $notification->data['profile_picture'])
        <img src="{{ asset('storage/' . $notification->data['profile_picture']) }}" 
             alt="{{ isset($notification->data['name']) ? $notification->data['name'] : 'User' }}"
             class="w-9 h-9 rounded-full object-cover">
    @else
        <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center">
            <span class="text-xs text-gray-600">
                {{ isset($notification->data['name']) ? initials($notification->data['name']) : 'DU' }}
            </span>
        </div>
    @endif
</div>
            <div class="flex-1 min-w-0">
                <a href="{{ $notification->data['url'] ?? '#' }}" class="block group">
                    <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                        {!! $notification->data['message'] ?? 'No message available' !!}
                        @if(!$notification->read_at)<span class="unread-indicator"></span>@endif
                    </p>
                    <div class="flex flex-col text-xs text-gray-500 mt-1">
                        @if(isset($notification->data['user_id']))
                            <span>EmployeeID: {{ $notification->data['user_id'] }}</span>
                        @endif
                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            </div>
            
            <button class="ml-2 text-gray-400 hover:text-gray-600 transition-colors" onclick="deleteSingleNotification('{{ $notification->id }}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </li>
@endforeach
                                @foreach($terminationNotifications as $termination)
                                <li class="notification-item p-4 bg-red-50 hover:bg-red-100 transition-all" data-id="{{ $termination['id'] }}" data-read="true">
                                    <div class="flex items-start">
                                        <input type="checkbox" class="notification-checkbox mt-1 mr-3 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" value="{{ $termination['id'] }}">
                                        
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <a href="{{ $termination['url'] ?? '#' }}" class="block group">
                                                <p class="text-sm font-medium text-red-700 group-hover:text-red-800 transition-colors">
                                                    {{ $termination['details'] }}
                                                </p>
                                                <div class="flex flex-wrap items-center text-xs text-red-500 mt-1 gap-x-2">
                                                    <span>Termination Notice</span>
                                                    <span>•</span>
                                                    <span>{{ \Carbon\Carbon::parse($termination['created_at'])->diffForHumans() }}</span>
                                                </div>
                                            </a>
                                        </div>
                                        
                                        <button class="ml-2 text-red-300 hover:text-red-500 transition-colors" onclick="deleteSingleNotification('{{ $termination['id'] }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Empty State (hidden by default) -->
                    <div id="emptyFilterState" class="hidden text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-700">No unread notifications</h3>
                        <p class="mt-1 text-gray-500">You've read all your notifications.</p>
                        <button onclick="filterNotifications('all')" class="mt-4 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View all notifications →
                        </button>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Delete notifications</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">Are you sure you want to delete the selected notifications? This action cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                    Cancel
                </button>
                <form id="deleteForm" action="{{ route('admin.notifications.deleteSelected') }}" method="POST">
                    @csrf
                    <input type="hidden" name="selected_notifications" id="selectedNotifications">
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize tab states
        document.addEventListener('DOMContentLoaded', function() {
            filterNotifications('all');
            document.getElementById('allTab').classList.add('bg-white', 'shadow-sm', 'text-blue-600');
            
            // Highlight new notifications
            const unreadItems = document.querySelectorAll('.notification-item.unread');
            unreadItems.forEach(item => {
                item.classList.add('notification-highlight');
            });
        });

        function toggleCheckboxes(source) {
            let checkboxes = document.querySelectorAll('.notification-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
            updateDeleteButtonState();
        }

        function filterNotifications(type) {
            const allTab = document.getElementById('allTab');
            const unreadTab = document.getElementById('unreadTab');
            let allNotifications = document.querySelectorAll('.notification-item');
            let hasUnread = false;
            
            allNotifications.forEach(item => {
                if (type === 'all' || (type === 'unread' && item.getAttribute('data-read') === 'false')) {
                    item.style.display = 'flex';
                    if (type === 'unread' && item.getAttribute('data-read') === 'false') hasUnread = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Update tabs appearance
            allTab.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
            unreadTab.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
            
            if (type === 'all') {
                allTab.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
                document.getElementById('emptyFilterState').classList.add('hidden');
            } else {
                unreadTab.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
                document.getElementById('emptyFilterState').classList.toggle('hidden', hasUnread);
            }
        }

        function updateDeleteButtonState() {
            const selectedCount = document.querySelectorAll('.notification-checkbox:checked').length;
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            
            if (selectedCount > 0) {
                deleteBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete (${selectedCount})
                `;
            } else {
                deleteBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                `;
            }
        }

        function showDeleteModal() {
            let selected = Array.from(document.querySelectorAll('.notification-checkbox:checked'))
                               .map(cb => cb.value);
                               
            if (selected.length === 0) {
                alert('Please select at least one notification to delete.');
                return;
            }
            
            document.getElementById('selectedNotifications').value = selected.join(',');
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function deleteSingleNotification(id) {
            if (confirm('Are you sure you want to delete this notification?')) {
                document.getElementById('selectedNotifications').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        // Update delete button when checkboxes change
        document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateDeleteButtonState);
        });
        
        // Initialize delete button click handler
        document.getElementById('deleteSelectedBtn').addEventListener('click', showDeleteModal);
    </script>

</body>
</html>