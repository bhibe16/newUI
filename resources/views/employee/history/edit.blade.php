<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>Edit Employee Record</title>
    <!-- Import Roboto font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen font-roboto">
    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <!-- Flex container to hold sidebar and main content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 md:p-8">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6 md:p-8">
                <div class="mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user-edit mr-3 text-blue-600"></i>
                        Edit Employment History
                    </h1>
                    <p class="text-gray-600 mt-2">Update the employment record details below</p>
                </div>

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form to edit employee history -->
                <form action="{{ route('employee.history.update', $record->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Grid Form Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company Name -->
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-building mr-2 text-blue-500 text-sm"></i>
                                Company Name
                            </label>
                            <input type="text" id="company_name" name="company_name" 
                                   value="{{ old('company_name', $record->company_name) }}" 
                                   class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input focus:border-blue-500" 
                                   placeholder="Enter company name" required>
                            @error('company_name')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-briefcase mr-2 text-blue-500 text-sm"></i>
                                Position
                            </label>
                            <input type="text" id="position" name="position" 
                                   value="{{ old('position', $record->position) }}" 
                                   class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input focus:border-blue-500" 
                                   placeholder="Enter job position" required>
                            @error('position')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-500 text-sm"></i>
                                Address
                            </label>
                            <input type="text" id="address" name="address" 
                                   value="{{ old('address', $record->address) }}" 
                                   class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input focus:border-blue-500" 
                                   placeholder="Enter company address" required>
                            @error('address')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500 text-sm"></i>
                                Start Date
                            </label>
                            <div class="relative">
                                <input type="date" id="start_date" name="start_date" 
                                       value="{{ old('start_date', $record->start_date) }}" 
                                       class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input focus:border-blue-500" required>
                            </div>
                            @error('start_date')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500 text-sm"></i>
                                End Date
                            </label>
                            <div class="relative">
                                <input type="date" id="end_date" name="end_date" 
                                       value="{{ old('end_date', $record->end_date) }}" 
                                       class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input focus:border-blue-500" required>
                            </div>
                            @error('end_date')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ url()->previous() }}" 
                           class="px-6 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition duration-200 text-center">
                            <i class="fas fa-arrow-left mr-2"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-200 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Update History
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>