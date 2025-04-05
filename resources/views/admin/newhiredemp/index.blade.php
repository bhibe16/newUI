<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Employee Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #f59e0b;
            color: white;
            font-weight: bold;
        }
        .table-header {
            background-color: #f59e0b;
        }
        .table-row:hover {
            background-color: #f3f4f6;
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .pagination-btn {
            transition: all 0.2s ease;
        }
        .pagination-btn:hover:not(:disabled) {
            background-color: #f3f4f6;
        }
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .pagination-btn.active {
            background-color: #f59e0b;
            color: white;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <div class="flex-grow p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">New Hired Employees</h1>
                    <p class="text-gray-600">Manage and review recently hired employees</p>
                </div>
                <div class="flex items-center space-x-4">
                <div class="relative">
    <form id="searchForm" method="GET" action="{{ request()->url() }}">
        <input type="text" 
               name="search" 
               id="searchInput" 
               value="{{ $searchTerm ?? '' }}" 
               placeholder="Search employees..."
               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-300 w-64"
               onkeydown="handleSearch(event)">
        <div class="absolute left-3 top-2.5 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </form>
</div>
                </div>
            </div>

            <!-- Employee Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="table-header">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Profile</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Employee ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Full Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Position</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Email</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="employees-table-body">
                        @php
                            // Pagination logic
                            $currentPage = request()->get('page', 1);
                            $perPage = 7;
                            $offset = ($currentPage - 1) * $perPage;
                            $paginatedEmployees = array_slice($employees, $offset, $perPage);
                            $totalPages = ceil(count($employees) / $perPage);
                        @endphp
                        
                        @foreach ($paginatedEmployees as $employee)
                            @php
                                // Set default values for missing fields
                                $middleName = $employee['middle_name'] ?? '';
                                $salary = isset($employee['salary']) ? number_format(floatval($employee['salary']), 2) : 'N/A';
                            @endphp
                            
                            <tr class="table-row hover:bg-gray-50 cursor-pointer animate-fade-in" onclick="toggleModal('modal-{{ $employee['id'] }}')">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="avatar">
                                                {{ substr($employee['first_name'] ?? '', 0, 1) }}{{ substr($employee['last_name'] ?? '', 0, 1) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $employee['id'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee['first_name'] ?? '' }} {{ $employee['last_name'] ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $employee['job_position'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $employee['department'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="truncate max-w-[180px] inline-block">{{ $employee['email'] ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white rounded-b-lg shadow-sm">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">{{ $offset + 1 }}</span> to <span class="font-medium">{{ min($offset + $perPage, count($employees)) }}</span> of <span class="font-medium">{{ count($employees) }}</span> results
                </div>
                <div class="flex space-x-1">
                    <button onclick="changePage(1)" class="pagination-btn px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white" {{ $currentPage == 1 ? 'disabled' : '' }}>
                        &laquo;
                    </button>
                    <button onclick="changePage({{ $currentPage - 1 }})" class="pagination-btn px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white" {{ $currentPage == 1 ? 'disabled' : '' }}>
                        &lsaquo;
                    </button>
                    
                    @for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                        <button onclick="changePage({{ $i }})" class="pagination-btn px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white {{ $i == $currentPage ? 'active' : '' }}">
                            {{ $i }}
                        </button>
                    @endfor
                    
                    <button onclick="changePage({{ $currentPage + 1 }})" class="pagination-btn px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white" {{ $currentPage == $totalPages ? 'disabled' : '' }}>
                        &rsaquo;
                    </button>
                    <button onclick="changePage({{ $totalPages }})" class="pagination-btn px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white" {{ $currentPage == $totalPages ? 'disabled' : '' }}>
                        &raquo;
                    </button>
                </div>
            </div>

            <!-- Modals - Only for the displayed employees -->
            @foreach ($paginatedEmployees as $employee)
                @php
                    // Set default values for modal
                    $middleName = $employee['middle_name'] ?? '';
                    $salary = isset($employee['salary']) ? number_format(floatval($employee['salary']), 2) : 'N/A';
                    $birthDate = $employee['birth_date'] ?? 'N/A';
                    $gender = $employee['gender'] ?? 'N/A';
                    $contact = $employee['contact'] ?? 'N/A';
                @endphp
            
                <div id="modal-{{ $employee['id'] }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center border-b border-gray-200 p-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Employee Details</h2>
                                <p class="text-gray-600">ID: {{ $employee['id'] ?? 'N/A' }}</p>
                            </div>
                            <button class="text-gray-400 hover:text-gray-500" onclick="toggleModal('modal-{{ $employee['id'] }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div class="flex items-start space-x-6">
                                <div class="avatar" style="width: 80px; height: 80px; font-size: 24px;">
                                    {{ substr($employee['first_name'] ?? '', 0, 1) }}{{ substr($employee['last_name'] ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">{{ $employee['first_name'] ?? '' }} {{ $middleName }} {{ $employee['last_name'] ?? '' }}</h3>
                                    <p class="text-gray-600">{{ $employee['job_position'] ?? 'N/A' }}</p>
                                    <p class="text-gray-600">{{ $employee['department'] ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Personal Information</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-sm text-gray-500">Birth Date</p>
                                                <p class="text-gray-800">{{ $birthDate }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Gender</p>
                                                <p class="text-gray-800">{{ $gender }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Contact</p>
                                                <p class="text-gray-800">{{ $contact }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Email</p>
                                                <p class="text-gray-800">{{ $employee['email'] ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Employment Details</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-sm text-gray-500">Job Position</p>
                                                <p class="text-gray-800">{{ $employee['job_position'] ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Department</p>
                                                <p class="text-gray-800">{{ $employee['department'] ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Salary</p>
                                                <p class="text-gray-800">{{ $salary }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-4 flex justify-end space-x-3">
                            <button onclick="toggleModal('modal-{{ $employee['id'] }}')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Close
                            </button>
                            <button class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        let searchTimer;
    
    function handleSearch(event) {
        // Submit form on Enter key
        if (event.key === 'Enter') {
            event.preventDefault();
            submitSearch();
            return;
        }
        
        // Clear previous timer
        clearTimeout(searchTimer);
        
        // Set new timer for 1 second after typing stops
        searchTimer = setTimeout(() => {
            submitSearch();
        }, 1000);
    }
    
    function submitSearch() {
        const form = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        
        // Reset to page 1 when searching
        const pageInput = document.createElement('input');
        pageInput.type = 'hidden';
        pageInput.name = 'page';
        pageInput.value = '1';
        form.appendChild(pageInput);
        
        form.submit();
    }
    
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden', !modal.classList.contains('hidden'));
    }
    
    function changePage(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        
        // Preserve search term if it exists
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value) {
            url.searchParams.set('search', searchInput.value);
        }
        
        window.location.href = url.toString();
    }
    </script>
</body>
</html>