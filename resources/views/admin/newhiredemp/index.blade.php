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
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
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
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .status-select {
            transition: all 0.3s ease;
        }
        .status-select.pending {
            background-color: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }
        .status-select.approved {
            background-color: #dcfce7;
            color: #166534;
            border-color: #10b981;
        }
        .status-select.rejected {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
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
                        <input type="text" id="searchInput" placeholder="Search employees..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-300 w-64">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-500">Total Employees</p>
                            <h3 id="total-count" class="text-2xl font-bold text-gray-800">{{ count($employees) }}</h3>
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
                            <h3 id="pending-count" class="text-2xl font-bold text-gray-800">{{ $pendingCount }}</h3>
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
                            <h3 id="approved-count" class="text-2xl font-bold text-gray-800">{{ $approvedCount }}</h3>
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
                            <h3 id="rejected-count" class="text-2xl font-bold text-gray-800">{{ $rejectedCount }}</h3>
                        </div>
                        <div class="bg-red-100 p-3 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="employees-table-body">
                        @foreach ($employees as $employee)
                            @php
                                // Set default values for missing fields
                                $middleName = $employee['middle_name'] ?? '';
                                $salary = isset($employee['salary']) ? number_format(floatval($employee['salary']), 2) : 'N/A';
                                $status = $employee['status'] ?? 'pending';
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="status-select w-full px-2 py-1 rounded-md text-xs border focus:outline-none status-{{ $status }}" 
                                        data-employee-id="{{ $employee['id'] }}"
                                        data-current-status="{{ $status }}"
                                        onchange="updateStatus(this)" onclick="event.stopPropagation();">
                                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white rounded-b-lg shadow-sm">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">{{ count($employees) }}</span> of <span class="font-medium">{{ count($employees) }}</span> results
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </button>
                </div>
            </div>

            <!-- Modals -->
            @foreach ($employees as $employee)
                @php
                    // Set default values for modal
                    $middleName = $employee['middle_name'] ?? '';
                    $salary = isset($employee['salary']) ? number_format(floatval($employee['salary']), 2) : 'N/A';
                    $birthDate = $employee['birth_date'] ?? 'N/A';
                    $gender = $employee['gender'] ?? 'N/A';
                    $contact = $employee['contact'] ?? 'N/A';
                    $status = $employee['status'] ?? 'pending';
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
                                            <div>
                                                <p class="text-sm text-gray-500">Status</p>
                                                <select class="status-select px-3 py-1 rounded-full text-sm font-medium status-{{ $status }}" 
                                                    data-employee-id="{{ $employee['id'] }}"
                                                    data-current-status="{{ $status }}"
                                                    onchange="updateStatus(this, '{{ $employee['id'] }}')">
                                                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
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
         async function updateStatus(selectElement, employeeId = null) {
            if (!employeeId) {
                employeeId = selectElement.getAttribute('data-employee-id');
            }
            
            const status = selectElement.value;
            const previousStatus = selectElement.getAttribute('data-current-status');
            
            // Show loading state
            selectElement.disabled = true;
            const originalText = selectElement.innerHTML;
            selectElement.innerHTML = '<span class="loading-spinner"></span>';
            
            try {
                const response = await fetch(`/api/employees/${employeeId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update status');
                }
                
                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg';
                successMessage.textContent = 'Status updated successfully! Refreshing...';
                document.body.appendChild(successMessage);
                
                // Update UI temporarily
                selectElement.classList.remove(`status-${previousStatus}`);
                selectElement.classList.add(`status-${status}`);
                selectElement.setAttribute('data-current-status', status);
                
                // Update counts temporarily
                if (data.counts) {
                    document.getElementById('pending-count').textContent = data.counts.pending;
                    document.getElementById('approved-count').textContent = data.counts.approved;
                    document.getElementById('rejected-count').textContent = data.counts.rejected;
                    document.getElementById('total-count').textContent = data.counts.total;
                }
                
                // Refresh the page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } catch (error) {
                console.error('Error:', error);
                // Revert on error
                selectElement.value = previousStatus;
                selectElement.classList.remove(`status-${status}`);
                selectElement.classList.add(`status-${previousStatus}`);
                alert('Failed to update status: ' + error.message);
            } finally {
                selectElement.innerHTML = originalText;
                selectElement.disabled = false;
            }
        }
        

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#employees-table-body tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden', !modal.classList.contains('hidden'));
        }
    </script>
</body>
</html>