<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Document Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Fix for the dropdown shadow/double border */
        select[name="status"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">

    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 sm:p-8 transition-all duration-300">
            <div class="p-6 rounded-2xl bg-white shadow-sm max-w-7xl mx-auto">
                
                <!-- Title & Header Section -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Documents Pending Review</h1>
                            <p class="text-gray-500 mt-1">Manage and review employee documents</p>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <!-- Toggle Layout Button -->
                            <button id="toggleButton" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm" onclick="toggleLayout()">
                                <i class="fas fa-th"></i>
                                <span>Card View</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filter & Search Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 p-4 rounded-xl">
                        <form id="filterForm" action="{{ route('admin.documents.index') }}" method="GET" class="w-full sm:w-auto">
                            <div class="relative">
                                <select name="status" id="statusFilter" class="border border-gray-300 rounded-lg pl-4 pr-10 py-2 bg-white focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 w-full sm:w-48">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Documents</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                        </form>

                        <div class="relative w-full sm:w-72">
                            <input type="text" id="searchInput" class="border border-gray-300 rounded-lg pl-10 pr-4 py-2 w-full focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 shadow-sm" placeholder="Search employee...">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>

                @if($documents->isEmpty())
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-file-alt text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-1">No documents found</h3>
                        <p class="text-gray-500">There are no documents matching your current filters.</p>
                    </div>
                @else
                <!-- Card Layout -->
                <div id="cardLayout" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($documents as $document)
                        @php
                            $employee = optional($document->Employee);
                            $statusColors = [
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'pending' => 'bg-yellow-100 text-yellow-800'
                            ];
                        @endphp
                        <div onclick="window.location='{{ route('admin.documents.view', $document) }}'" class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden">
                            <div class="p-5">
                                <!-- Employee Info -->
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="relative">
                                        <img src="{{ $employee && $employee->profile_picture 
                                        ? asset('storage/' . $employee->profile_picture) 
                                        : asset('default-avatar.png') }}" 
                                        alt="Employee Picture" 
                                        class="w-12 h-12 rounded-full border-2 border-white shadow-sm object-cover">
                                        <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full {{ $statusColors[$document->status] }} border-2 border-white"></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $employee->position->name }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Details -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Document Type</span>
                                        <span class="text-sm font-semibold text-gray-700 truncate max-w-[120px]">
                                            @foreach(App\Models\Document::DOCUMENT_TYPES as $category => $types)
                                                @if(array_key_exists($document->document_type, $types))
                                                    {{ $types[$document->document_type] }}
                                                    @break
                                                @endif
                                            @endforeach
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Uploaded</span>
                                        <span class="text-sm text-gray-600">{{ $document->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Status</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$document->status] }}">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-xs text-gray-500">ID: {{ $document->id }}</span>
                                <button class="text-yellow-600 hover:text-yellow-700 text-sm font-medium flex items-center gap-1">
                                    Review <i class="fas fa-chevron-right text-xs mt-0.5"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Table Layout -->
                <div id="tableLayout" class="hidden">
                    <div class="overflow-x-auto bg-white rounded-xl shadow-sm border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($documents as $document)
                                    @php
                                        $employee = optional($document->Employee);
                                        $statusColors = [
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.documents.view', $document) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 relative">
                                                    <img src="{{ $employee && $employee->profile_picture 
                                                    ? asset('storage/' . $employee->profile_picture) 
                                                    : asset('default-avatar.png') }}" 
                                                    alt="Employee Picture" 
                                                    class="h-10 w-10 rounded-full border border-gray-200 object-cover">
                                                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full {{ $statusColors[$document->status] }} border-2 border-white"></span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $employee->position->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">
                                                @foreach(App\Models\Document::DOCUMENT_TYPES as $category => $types)
                                                    @if(array_key_exists($document->document_type, $types))
                                                        {{ $types[$document->document_type] }}
                                                        @break
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $document->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $document->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$document->status] }}">
                                                {{ ucfirst($document->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-yellow-600 hover:text-yellow-800 flex items-center gap-1">
                                                Review <i class="fas fa-chevron-right text-xs mt-0.5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @endif

                <!-- Pagination would go here if needed -->
                @if($documents->hasPages())
                <div class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing {{ $documents->firstItem() }} to {{ $documents->lastItem() }} of {{ $documents->total() }} results
                    </div>
                    <div class="flex space-x-2">
                        @if($documents->onFirstPage())
                            <span class="px-3 py-1 rounded border border-gray-200 text-gray-400 cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $documents->previousPageUrl() }}" class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50 transition">Previous</a>
                        @endif

                        @foreach(range(1, $documents->lastPage()) as $page)
                            @if($page == $documents->currentPage())
                                <span class="px-3 py-1 rounded bg-yellow-500 text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $documents->url($page) }}" class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50 transition">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($documents->hasMorePages())
                            <a href="{{ $documents->nextPageUrl() }}" class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50 transition">Next</a>
                        @else
                            <span class="px-3 py-1 rounded border border-gray-200 text-gray-400 cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        // Layout Toggle Functionality
        function toggleLayout() {
            const cardLayout = document.getElementById('cardLayout');
            const tableLayout = document.getElementById('tableLayout');
            const toggleButton = document.getElementById('toggleButton');
            const icon = toggleButton.querySelector('i');
            const textSpan = toggleButton.querySelector('span');

            cardLayout.classList.toggle('hidden');
            tableLayout.classList.toggle('hidden');

            if (cardLayout.classList.contains('hidden')) {
                icon.className = 'fas fa-th-large';
                textSpan.textContent = 'Card View';
                localStorage.setItem('layout', 'table');
            } else {
                icon.className = 'fas fa-list';
                textSpan.textContent = 'Table View';
                localStorage.setItem('layout', 'card');
            }
        }

        // Initialize layout based on localStorage
        window.onload = function() {
            const savedLayout = localStorage.getItem('layout') || 'card';
            const cardLayout = document.getElementById('cardLayout');
            const tableLayout = document.getElementById('tableLayout');
            const toggleButton = document.getElementById('toggleButton');
            const icon = toggleButton.querySelector('i');
            const textSpan = toggleButton.querySelector('span');

            if (savedLayout === 'table') {
                cardLayout.classList.add('hidden');
                tableLayout.classList.remove('hidden');
                icon.className = 'fas fa-th-large';
                textSpan.textContent = 'Card View';
            } else {
                cardLayout.classList.remove('hidden');
                tableLayout.classList.add('hidden');
                icon.className = 'fas fa-list';
                textSpan.textContent = 'Table View';
            }

            // Filter functionality with 1-second delay
            document.getElementById('statusFilter').addEventListener('change', function() {
                setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 1000); // 1-second delay
            });

            // Search functionality
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchEmployee();
                }
            });
        }

        function searchEmployee() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('#cardLayout > div');
            const rows = document.querySelectorAll('#tableLayout tbody tr');

            cards.forEach(card => {
                const name = card.querySelector('.font-semibold').textContent.toLowerCase();
                card.style.display = name.includes(searchTerm) ? '' : 'none';
            });

            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(1) .text-gray-900').textContent.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        }
    </script>
</body>
</html>