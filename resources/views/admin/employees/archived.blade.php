<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIS - Archived Employees</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50">

    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Archived Employees</h1>
                        <p class="text-gray-600">View and manage archived employee records</p>
                    </div>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        <div>
                            <p class="font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Card Container -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Table Container -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                        <thead class="bg-yellow-500">
                                <tr class="text-left text-gray-600 uppercase text-sm">  
                                    <th class="p-4 font-medium">Employee</th>
                                    <th class="p-4 font-medium">ID</th>
                                    <th class="p-4 font-medium">Department</th>
                                    <th class="p-4 font-medium">Position</th>
                                    <th class="p-4 font-medium">Contact</th>
                                    <th class="p-4 font-medium text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($employees as $employee)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4">
                                            <div class="flex items-center">
                                                <img src="{{ asset('storage/' . $employee->profile_picture) }}"
                                                    alt="Profile Picture" 
                                                    class="w-10 h-10 rounded-full object-cover mr-3">
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-gray-600">{{ $employee->user_id}}</td>
                                        <td class="p-4">
                                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">
                                                {{ $employee->department->name }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-gray-600">{{ $employee->position->name }}</td>
                                        <td class="p-4 text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                                {{ $employee->email }}
                                            </div>
                                        </td>
                                        <td class="p-4 text-right">
                                            <div class="flex justify-end space-x-2">
                                                <form action="{{ route('employees.restore', $employee->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors flex items-center"
                                                            title="Restore Employee">
                                                        <i class="fas fa-undo mr-2"></i>
                                                        Restore
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    @if($employees->isEmpty())
                        <div class="p-12 text-center">
                            <i class="fas fa-archive text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-500">No archived employees found</h3>
                            <p class="text-gray-400 mt-1">When you archive employees, they will appear here</p>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($employees->isNotEmpty())
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} results
                        </div>
                        <div class="bg-white px-4 py-3 rounded-lg shadow-sm border border-gray-200">
                            {{ $employees->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>