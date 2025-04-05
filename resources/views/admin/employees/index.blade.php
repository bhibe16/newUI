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
        /* Custom styles for enhanced UX */
        .main-content {
            background-color: #f3f4f6; /* Changed to gray background */
        }
        
        .employee-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .employee-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .status-select {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .status-select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3);
        }
        
        .table-header {
            background-color: #ffffff;
            position: sticky;
            top: 0;
        }
        
        .table-row:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .search-container {
            transition: all 0.3s ease;
            border-radius: 10px;
            max-width: 500px;
            width: 100%;
        }
        
        .search-container:focus-within {
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3);
            border-color: #f59e0b;
        }
        
        .search-input {
            width: 100%;
            padding-right: 110px;
        }
        
        .search-button {
            width: 100px;
        }
        
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #f59e0b #f3f4f6;
        }
        
        .modal-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-content::-webkit-scrollbar-track {
            background: #f3f4f6;
        }
        
        .modal-content::-webkit-scrollbar-thumb {
            background-color: #f59e0b;
            border-radius: 10px;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(10px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .info-label {
            color: #6b7280;
            font-weight: 500;
            width: 160px;
            display: inline-block;
        }
        
        .info-value {
            color: #1f2937;
        }
        
        .section-divider {
            border-color: #e5e7eb;
            border-style: dashed;
        }
        
        /* Pagination styles */
        .pagination {
            background-color: white;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #e5e7eb;
            border-color: #d1d5db;
            color: #374151;
        }
        
        .pagination .page-link {
            color: #374151;
            border-color: #d1d5db;
        }
        
        .pagination .page-link:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="main-content min-h-screen">
    
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <main class="flex-grow p-6">

<!-- Header Section -->
<div class="flex justify-end w-full mb-4">
    <div class="flex flex-row gap-3">
        <button id="toggleButton"
            class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center gap-2"
            onclick="toggleLayout()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span id="toggleText">Table View</span>
        </button>
    </div>
</div>


<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-4 mb-6 w-full">
    <!-- Title Section (Left) -->
    <div class="w-full md:w-1/2">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Employee Directory</h1>
        <p class="text-gray-600 mt-1">Manage your organization's employees</p>
    </div>

    <!-- Search Bar Section (Right) -->
<div class="w-full md:w-auto min-w-[300px] md:min-w-[350px] lg:min-w-[400px] xl:min-w-[500px]">
    <form method="GET" action="{{ route('admin.employees.index') }}" class="flex items-center">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                class="block w-full pl-10 pr-12 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-300"
                placeholder="Search by name, ID, department, or status">
            <button type="submit"
                class="absolute inset-y-0 right-0 px-4 text-white bg-yellow-500 rounded-r-lg hover:bg-yellow-600 flex items-center justify-center">
                Search
            </button>
        </div>
    </form>
</div>

</div>


<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between">
            <div>
                <p class="text-gray-500">Total Employees</p>
                <h3 id="total-count" class="text-2xl font-bold text-gray-800">{{ $employees->total() }}</h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between">
            <div>
                <p class="text-gray-500">Pending Approval</p>
                <h3 id="pending-count" class="text-2xl font-bold text-gray-800">{{ $statusCounts['pending'] ?? 0 }}</h3>
            </div>
            <div class="bg-amber-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between">
            <div>
                <p class="text-gray-500">Approved</p>
                <h3 id="approved-count" class="text-2xl font-bold text-gray-800">{{ $statusCounts['approved'] ?? 0 }}</h3>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between">
            <div>
                <p class="text-gray-500">Rejected</p>
                <h3 id="rejected-count" class="text-2xl font-bold text-gray-800">{{ $statusCounts['reject'] ?? 0 }}</h3>
            </div>
            <div class="bg-red-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Success Message -->
@if (session('success'))
    <div class="bg-green-500 text-white p-4 rounded-lg flex items-center mb-6 animate-fade-in">
        <svg class="w-6 h-6 mr-2 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
        <div>
            <p class="font-medium">{{ session('success') }}</p>
            <p class="text-sm opacity-90">Employee record updated successfully</p>
        </div>
    </div>
@endif


            <!-- Card Layout -->
            <div id="cardLayout" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($employees as $employee)
                    <div class="employee-card bg-white rounded-lg shadow-md hover:shadow-lg cursor-pointer relative animate-fade-in-up"
                        onclick="toggleModal('modal-{{ $employee->id }}')">
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3 z-10">
                            <select class="status-select text-center px-2 py-1 rounded-md text-xs border focus:outline-none 
                                {{ $employee->status === 'approved' ? 'bg-green-200 text-green-700 border-green-500' : '' }}
                                {{ $employee->status === 'reject' ? 'bg-red-200 text-red-700 border-red-500' : '' }}
                                {{ $employee->status === 'pending' ? 'bg-blue-200 text-blue-700 border-blue-500' : '' }}"
                                data-employee-id="{{ $employee->id }}"
                                onchange="updateStatus(this)" onclick="event.stopPropagation();">
                                <option value="pending" @selected($employee->status == 'pending')>Pending</option>
                                <option value="approved" @selected($employee->status == 'approved')>Approved</option>
                                <option value="reject" @selected($employee->status == 'reject')>Reject</option>
                            </select>
                        </div>

                        <!-- Employee Content -->
                        <div class="p-5 flex flex-col items-center">
                            <!-- Profile Picture -->
                            <div class="relative mb-4">
                                <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt="Profile Picture"
                                    class="w-20 h-20 rounded-full border-2 border-yellow-400 object-cover shadow-md">
                            </div>
                            
                            <!-- Employee Info -->
                            <div class="text-center mb-4 w-full">
                                <h3 class="font-bold text-lg text-gray-800 mb-1 truncate">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                                <p class="text-gray-600 text-sm font-medium truncate">{{ $employee->position->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $employee->department->name }}</p>
                            </div>
                            
                            <!-- Details -->
                            <div class="w-full bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-gray-500">Employee ID:</span>
                                    <span class="text-xs font-medium text-gray-800">{{ $employee->user_id }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-500">Email:</span>
                                    <span class="text-xs font-medium text-gray-800 truncate max-w-[120px]">{{ $employee->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-700">No employees found</h3>
                            <p class="mt-1 text-gray-500">Try adjusting your search or add a new employee</p>
                            <a href="{{ route('register') }}" class="mt-4 text-yellow-600 hover:text-yellow-700 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Employee
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Table Layout -->
            <div id="tableLayout" class="hidden overflow-x-auto rounded-lg border border-gray-200 shadow-sm bg-white">
            <table class="min-w-full divide-y divide-gray-200">
    <thead class="table-header">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black-800 uppercase tracking-wider bg-yellow-500">Profile</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black-800 uppercase tracking-wider bg-yellow-500">Employee ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black-800 uppercase tracking-wider bg-yellow-500">Full Name</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black-800 uppercase tracking-wider bg-yellow-500">Position</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black-800 uppercase tracking-wider bg-yellow-500">Department</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black-800 uppercase tracking-wider bg-yellow-500">Email</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black-800 uppercase tracking-wider bg-yellow-500">Status</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($employees as $employee)
            <tr class="table-row hover:bg-gray-50 cursor-pointer animate-fade-in" onclick="toggleModal('modal-{{ $employee->id }}')">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt="Profile Picture"
                                class="h-12 w-12 rounded-full object-cover border border-yellow-200">
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $employee->user_id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $employee->position->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $employee->department->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="truncate max-w-[180px] inline-block">{{ $employee->email }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <select class="status-select w-full px-2 py-1 rounded-md text-xs border focus:outline-none 
                        {{ $employee->status === 'approved' ? 'bg-green-200 text-green-700 border-green-500' : '' }}
                        {{ $employee->status === 'reject' ? 'bg-red-200 text-red-700 border-red-500' : '' }}
                        {{ $employee->status === 'pending' ? 'bg-blue-200 text-blue-700 border-blue-500' : '' }}"
                        data-employee-id="{{ $employee->id }}"
                        onchange="updateStatus(this)" onclick="event.stopPropagation();">
                        <option value="pending" @selected($employee->status == 'pending')>Pending</option>
                        <option value="approved" @selected($employee->status == 'approved')>Approved</option>
                        <option value="reject" @selected($employee->status == 'reject')>Reject</option>
                    </select>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-700">No employees found</h3>
                        <p class="mt-1 text-xs text-gray-500">Try adjusting your search query</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
            <div class="mt-8 flex justify-center pagination">
                <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    @if ($employees->onFirstPage())
                        <span class="inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-gray-300 cursor-not-allowed">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $employees->previousPageUrl() }}" class="inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    @foreach(range(1, $employees->lastPage()) as $i)
                        @if($i == $employees->currentPage())
                            <span class="inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-100 text-gray-700 font-medium">{{ $i }}</span>
                        @else
                            <a href="{{ $employees->url($i) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">{{ $i }}</a>
                        @endif
                    @endforeach

                    @if ($employees->hasMorePages())
                        <a href="{{ $employees->nextPageUrl() }}" class="inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-gray-300 cursor-not-allowed">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </nav>
            </div>
            @endif

            <!-- Employee Detail Modals -->
            @foreach ($employees as $employee)
                <div id="modal-{{ $employee->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="modal-overlay absolute w-full h-full bg-gray-900 bg-opacity-50"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="modal-content bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                            <!-- Modal Header -->
                            <div class="bg-white px-6 py-4 flex justify-between items-center border-b border-gray-200">
                                <h3 class="text-xl font-bold text-gray-800">Employee Details</h3>
                                <button onclick="toggleModal('modal-{{ $employee->id }}')" class="text-gray-700 hover:text-gray-900">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Modal Body -->
                            <div class="p-6 space-y-6">
                                <!-- Profile Section -->
                                <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                                             alt="Profile Picture"
                                             class="w-32 h-32 rounded-full border-4 border-gray-200 object-cover shadow-lg">
                                    </div>
                                    <div class="flex-grow">
                                        <h2 class="text-2xl font-bold text-gray-800">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                                        <p class="text-lg text-gray-600">{{ $employee->position->name }}</p>
                                        <p class="text-gray-500">{{ $employee->department->name }}</p>
                                        
                                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <p><span class="info-label">Employee ID:</span> <span class="info-value">{{ $employee->user_id }}</span></p>
                                                <p><span class="info-label">Status:</span> 
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $employee->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $employee->status === 'reject' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $employee->status === 'pending' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                        {{ ucfirst($employee->status) }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div>
                                                <p><span class="info-label">Email:</span> <span class="info-value">{{ $employee->email }}</span></p>
                                                <p><span class="info-label">Phone:</span> <span class="info-value">{{ $employee->phone ?? 'N/A' }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="section-divider my-6">
                                
                                <!-- Personal Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <p><span class="info-label">First Name:</span> <span class="info-value">{{ $employee->first_name }}</span></p>
                                        <p><span class="info-label">Middle Name:</span> <span class="info-value">{{ $employee->middle_name ?? 'N/A' }}</span></p>
                                        <p><span class="info-label">Last Name:</span> <span class="info-value">{{ $employee->last_name }}</span></p>
                                        <p><span class="info-label">Date of Birth:</span> <span class="info-value">{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('M d, Y') : 'N/A' }}</span></p>
                                        <p><span class="info-label">Gender:</span> <span class="info-value">{{ $employee->gender ?? 'N/A' }}</span></p>
                                        <p><span class="info-label">Address:</span> <span class="info-value">{{ $employee->address ?? 'N/A' }}</span></p>
                                    </div>
                                </div>
                                
                                <hr class="section-divider my-6">
                                
                                <!-- Employment Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Employment Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <p><span class="info-label">Department:</span> <span class="info-value">{{ $employee->department->name }}</span></p>
                                        <p><span class="info-label">Position:</span> <span class="info-value">{{ $employee->position->name }}</span></p>
                                        <p><span class="info-label">Employment Status:</span> <span class="info-value">{{ ucfirst($employee->employment_status) }}</span></p>
                                        <p><span class="info-label">Hire Date:</span> <span class="info-value">{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'N/A' }}</span></p>
                                    </div>
                                </div>
                                
                                <!-- Work Experience & Education -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                                    <!-- Work Experience -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            Work Experience
                                        </h3>
                                        
                                        @if(count($employment[$employee->user_id] ?? []) > 0)
                                            <div class="space-y-4">
                                                @foreach($employment[$employee->user_id] as $exp)
                                                <div class="pl-6 relative">
                                                    <div class="absolute left-3 top-3 bottom-0 w-0.5 bg-gray-300"></div>
                                                    <div class="relative">
                                                        <div class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-gray-400"></div>
                                                        <div class="bg-white p-3 rounded-lg shadow-xs border border-gray-100">
                                                            <h4 class="font-medium text-gray-800">{{ $exp->position }} at {{ $exp->company_name }}</h4>
                                                            <p class="text-sm text-gray-600 mt-1">
                                                                {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : '' }} - 
                                                                {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Present' }}
                                                                @if($exp->start_date && $exp->end_date)
                                                                    ({{ \Carbon\Carbon::parse($exp->start_date)->diffInMonths(\Carbon\Carbon::parse($exp->end_date)) }} months)
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-gray-500 text-center py-4">No work experience recorded</p>
                                        @endif
                                    </div>
                                    
                                    <!-- Education -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 w-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                                            </svg>
                                            Education
                                        </h3>
                                        
                                        @if(count($educational[$employee->user_id] ?? []) > 0)
                                            <div class="space-y-4">
                                                @foreach($educational[$employee->user_id] as $edu)
                                                <div class="pl-6 relative">
                                                    <div class="absolute left-3 top-3 bottom-0 w-0.5 bg-gray-300"></div>
                                                    <div class="relative">
                                                        <div class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-gray-400"></div>
                                                        <div class="bg-white p-3 rounded-lg shadow-xs border border-gray-100">
                                                            <h4 class="font-medium text-gray-800">{{ $edu->education_level }}</h4>
                                                            <p class="text-sm text-gray-600">{{ $edu->school_name }}</p>
                                                            <p class="text-sm text-gray-600 mt-1">
                                                                {{ $edu->start_year ? \Carbon\Carbon::parse($edu->start_year)->format('Y') : '' }} - 
                                                                {{ $edu->end_year ? \Carbon\Carbon::parse($edu->end_year)->format('Y') : 'Present' }}
                                                            </p>
                                                            @if($edu->graduation_status)
                                                            <p class="text-xs text-gray-500 mt-1">Status: {{ $edu->graduation_status }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-gray-500 text-center py-4">No education information recorded</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal Footer -->
                            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-200">
                                <div>
                                    <p class="text-sm text-gray-500">Last updated: {{ $employee->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="flex space-x-3">
                                    <button onclick="toggleModal('modal-{{ $employee->id }}')" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">
                                        Close
                                    </button>
                                    <form action="{{ url('admin/employees/' . $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Record
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </main>
    </div>

    <script>
        // Initialize the page with saved layout preference
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial layout from localStorage or default to card view
            const savedLayout = localStorage.getItem('layout') || 'card';
            toggleLayout(savedLayout, true);
            
            // Add animation to cards when they appear
            animateCards();
        });

        // Toggle between card and table layouts
        function toggleLayout(layout = null, initialLoad = false) {
            const cardLayout = document.getElementById('cardLayout');
            const tableLayout = document.getElementById('tableLayout');
            const toggleText = document.getElementById('toggleText');
            
            // Determine which layout to show
            if (!layout) {
                layout = cardLayout.classList.contains('hidden') ? 'card' : 'table';
            }
            
            if (layout === 'card') {
                cardLayout.classList.remove('hidden');
                tableLayout.classList.add('hidden');
                toggleText.textContent = 'Table View';
                if (!initialLoad) {
                    localStorage.setItem('layout', 'card');
                    animateCards();
                }
            } else {
                cardLayout.classList.add('hidden');
                tableLayout.classList.remove('hidden');
                toggleText.textContent = 'Card View';
                if (!initialLoad) {
                    localStorage.setItem('layout', 'table');
                }
            }
        }

        // Animate cards when they appear
        function animateCards() {
            const cards = document.querySelectorAll('.employee-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.05}s`;
                card.classList.add('animate-fade-in-up');
            });
        }

        // Toggle modal visibility
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden', !modal.classList.contains('hidden'));
            
            // Add animation when modal opens
            if (!modal.classList.contains('hidden')) {
                const modalContent = modal.querySelector('.modal-content');
                modalContent.classList.add('animate-fade-in');
            }
        }

        // Update employee status with AJAX
async function updateStatus(selectElement) {
    const employeeId = selectElement.dataset.employeeId;
    const newStatus = selectElement.value;
    
    // Save original state for rollback
    const originalValue = selectElement.value;
    const originalClass = selectElement.className;
    
    // Optimistic UI update
    selectElement.disabled = true;
    selectElement.className = originalClass.replace(
        /bg-(green|red|blue)-200 text-(green|red|blue)-700 border-(green|red|blue)-500/g, 
        ''
    );
    
    // Apply new styling based on selection
    if (newStatus === 'approved') {
        selectElement.classList.add('bg-green-200', 'text-green-700', 'border-green-500');
    } else if (newStatus === 'reject') {
        selectElement.classList.add('bg-red-200', 'text-red-700', 'border-red-500');
    } else {
        selectElement.classList.add('bg-blue-200', 'text-blue-700', 'border-blue-500');
    }
    
    try {
        const response = await fetch(`/employees/${employeeId}/update-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });
        
        if (!response.ok) throw new Error('Update failed');
        
        const data = await response.json();
        
        // Update stats cards if counts were returned
        if (data.counts) {
            document.getElementById('total-count').textContent = data.counts.total;
            document.getElementById('pending-count').textContent = data.counts.pending;
            document.getElementById('approved-count').textContent = data.counts.approved;
            document.getElementById('rejected-count').textContent = data.counts.reject;
        }
        
        // Show success notification
        showNotification('Status updated successfully', 'success');
        
    } catch (error) {
        console.error('Error:', error);
        // Revert UI on error
        selectElement.value = originalValue;
        selectElement.className = originalClass;
        
        // Show error notification
        showNotification('Failed to update status', 'error');
    } finally {
        selectElement.disabled = false;
    }
}

        // Show notification toast
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 px-4 py-2 rounded-md shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } animate-fade-in`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Enhanced search functionality
        document.getElementById("searchInput").addEventListener("input", function() {
            const query = this.value.toLowerCase();
            const isCardView = !document.getElementById('cardLayout').classList.contains('hidden');
            
            if (isCardView) {
                searchCards(query);
            } else {
                searchTable(query);
            }
        });

        function searchCards(query) {
            const cards = document.querySelectorAll("#cardLayout > .employee-card");
            let anyVisible = false;
            
            cards.forEach(card => {
                const textContent = card.textContent.toLowerCase();
                if (query === '' || textContent.includes(query)) {
                    card.style.display = 'block';
                    anyVisible = true;
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function searchTable(query) {
            const rows = document.querySelectorAll("#tableLayout tbody tr");
            let anyVisible = false;
            
            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip message rows
                
                const textContent = row.textContent.toLowerCase();
                if (query === '' || textContent.includes(query)) {
                    row.style.display = 'table-row';
                    anyVisible = true;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                const openModal = document.querySelector('.modal:not(.hidden)');
                if (openModal) {
                    toggleModal(openModal.id);
                }
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const openModal = document.querySelector('.modal:not(.hidden)');
                if (openModal) {
                    toggleModal(openModal.id);
                }
            }
        });
    </script>
</body>
</html>