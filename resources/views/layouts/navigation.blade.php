<nav x-data="navigationState()" class="linear-gradient" x-cloak>
<script>
    function navigationState() {
        return {
            open: false,
            showAdminNotifications: false,
            showEmployeeNotifications: false,
            showChat: false,
            unreadCount: {{ Auth::user()->unreadNotifications->count() }},
            init() {
                // Initialize time immediately
                this.updateTime();
                // Set up time updater
                setInterval(() => this.updateTime(), 1000);
                
                // Close all dropdowns before page unload
                window.addEventListener('beforeunload', () => {
                    this.showAdminNotifications = false;
                    this.showEmployeeNotifications = false;
                    this.showChat = false;
                });
            },
            updateTime() {
                const timeElement = document.getElementById('current-time');
                if (timeElement) {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    timeElement.innerText = `${hours}:${minutes}:${seconds}`;
                }
            },
            markAsRead(notificationId) {
                fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                }).then(() => {
                    this.unreadCount--;
                });
            }
        };
    }
</script>
    <!-- Primary Navigation Menu -->
    <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo and Branding -->
            <div class="flex items-center space-x-3">
                <div class="shrink-0 flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="w-10 h-10 rounded-md">
                </div>
                <span class="text-black font-semibold text-lg hidden md:inline-block">HRIS</span>
            </div>

            <!-- Right Side Controls -->
            <div class="flex items-center space-x-4 md:space-x-6">
                <!-- Time Display -->
                <div class="hidden sm:flex items-center bg-white/10 px-3 py-1 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="current-time" class="text-black font-medium"></span>
                </div>

                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'hr3')
                <!-- Admin Notification Icon -->
                <div class="relative">
                    <button @click="showAdminNotifications = !showAdminNotifications" 
                            @click.outside="showAdminNotifications = false"
                            class="p-2 text-black hover:bg-white/20 rounded-full relative transition-colors duration-200"
                            aria-label="Notifications"
                            aria-haspopup="true"
                            :aria-expanded="showAdminNotifications">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <template x-if="unreadCount > 0">
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full" 
                                  x-text="unreadCount"></span>
                        </template>
                    </button>
                    <div x-show="showAdminNotifications" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl z-50 border border-gray-200"
                         style="display: none;">
                        <div class="p-3 text-sm font-semibold text-gray-700 bg-gray-50 border-b">Admin Notifications</div>
                        <div class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            <ul class="divide-y divide-gray-200">
                                @forelse(Auth::user()->unreadNotifications as $notification)
                                <li class="p-3 hover:bg-blue-50 transition-colors duration-150">
                                    <a href="{{ $notification->data['url'] }}" 
                                       @click="markAsRead('{{ $notification->id }}')" 
                                       class="block text-sm text-gray-700 hover:text-blue-600">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 pt-0.5">
                                                <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-2">
                                                <p class="text-sm">{{ $notification->data['message'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="p-3 text-center text-gray-500 text-sm">
                                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-2">No new notifications</p>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                        <a href="{{ route('admin.notifications') }}" class="block text-center text-blue-600 hover:bg-blue-50 p-3 text-sm font-medium border-t">View all notifications</a>
                    </div>
                </div>
                @elseif(Auth::user()->role == 'Employee')
                <!-- Employee Notification Icon -->
                <div class="relative">
                    <button @click="showEmployeeNotifications = !showEmployeeNotifications" 
                            @click.outside="showEmployeeNotifications = false"
                            class="p-2 text-black hover:bg-white/20 rounded-full relative transition-colors duration-200"
                            aria-label="Notifications"
                            aria-haspopup="true"
                            :aria-expanded="showEmployeeNotifications">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <template x-if="unreadCount > 0">
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full" 
                                  x-text="unreadCount"></span>
                        </template>
                    </button>
                    <div x-show="showEmployeeNotifications" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 border border-gray-200"
                         style="display: none;">
                        <div class="p-3 text-sm font-semibold text-gray-700 bg-gray-50 border-b">Notifications</div>
                        <div class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            <ul class="divide-y divide-gray-200">
                                @foreach (Auth::user()->notifications as $notification)
                                    <li class="p-3 hover:bg-blue-50 transition-colors duration-150">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 pt-0.5">
                                                @if ($notification->data['status'] == 'rejected')
                                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                @else
                                                    <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="ml-2">
                                                <p class="text-sm text-gray-700">{{ $notification->data['message'] }}</p>
                                                @if ($notification->data['status'] == 'rejected')
                                                    <p class="text-xs text-red-500 mt-1">Reason: {{ $notification->data['rejection_comment'] }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="#" class="block text-center text-blue-600 hover:bg-blue-50 p-3 text-sm font-medium border-t">View all notifications</a>
                    </div>
                </div>
                @endif

                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button @click="open = !open" 
                            @click.outside="open = false"
                            class="flex items-center space-x-2 focus:outline-none"
                            aria-label="User menu"
                            aria-haspopup="true"
                            :aria-expanded="open">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-blue-600 font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:inline-block text-black font-medium">{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 text-white" :class="{ 'transform rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5"
                         style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>