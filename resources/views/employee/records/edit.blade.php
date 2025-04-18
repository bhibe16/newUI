<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>Edit Record | HRIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #f3f4f6;
            --accent: #10b981;
            --error: #ef4444;
            --warning: #f7e04b;
        }
        
        .form-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .form-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(45deg, #f7e04b, #f4d03f);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .profile-upload {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
            border-radius: 10px;
            background-color: #f9fafb;
        }
        
        .profile-upload:hover {
            border-color: var(--primary);
            background-color: #f0f4ff;
        }
        
        .profile-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .form-input {
            transition: all 0.2s ease;
            border-radius: 8px;
        }
        
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .btn-primary {
            background-color: var(--primary);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background-color: #e5e7eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .required-field::after {
            content: "*";
            color: var(--error);
            margin-left: 4px;
        }
        
        .drag-active {
            background-color: #e0e7ff !important;
            border-color: var(--primary) !important;
        }
        
        @media (max-width: 768px) {
            .form-card {
                border-radius: 10px;
            }
            
            .card-header {
                padding: 1rem 1.25rem;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 md:p-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header with Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <a href="{{ route('employee.records.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-indigo-600 md:ml-2">Employee Records</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-gray-400 md:ml-2">Edit Record</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Employee Record</h1>
                            <p class="text-gray-600 mt-1">Update employee information and save changes</p>
                        </div>
                        <a href="{{ url()->previous() }}" class="btn-secondary flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </a>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-medium">Success!</p>
                            <p class="text-sm mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-medium">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-sm mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Edit Record Form -->
                <form action="{{ route('employee.records.update', $record->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Profile Picture Section -->
                    <div class="form-card bg-white">
                        <div class="card-header">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Profile Picture
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                                <div class="relative">
                                    @if($record->profile_picture)
                                        <img id="profile-preview" src="{{ asset('storage/' . $record->profile_picture) }}" alt="Profile Preview" class="profile-preview rounded-full">
                                    @else
                                        <div id="profile-preview" class="profile-preview rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1 w-full">
                                    <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Upload new photo</label>
                                    <div class="profile-upload rounded-lg p-4 text-center cursor-pointer" id="upload-area">
                                        <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/jpeg,image/png">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-sm text-gray-600">Click to browse or drag and drop</p>
                                            <p class="text-xs text-gray-500">JPG or PNG (Max. 2MB)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information Section -->
                    <div class="form-card bg-white">
                        <div class="card-header">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                                </svg>
                                Personal Information
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1 required-field">First Name</label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $record->first_name) }}" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $record->middle_name) }}"
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1 required-field">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $record->last_name) }}" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1 required-field">Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $record->date_of_birth) }}" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1 required-field">Gender</label>
                                    <select id="gender" name="gender" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                        <option value="">Select Gender</option>
                                        @foreach (['Male', 'Female', 'Other'] as $gender)
                                            <option value="{{ $gender }}" {{ old('gender', $record->gender) == $gender ? 'selected' : '' }}>
                                                {{ $gender }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                                    <select id="marital_status" name="marital_status"
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                        <option value="">Select Status</option>
                                        @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                            <option value="{{ $status }}" {{ old('marital_status', $record->marital_status) == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                                    <input type="text" id="nationality" name="nationality" value="{{ old('nationality', $record->nationality) }}"
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="form-card bg-white">
                        <div class="card-header">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                Contact Information
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1 required-field">Email</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $record->email) }}" required readonly
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1 required-field">Phone</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $record->phone) }}" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                                        placeholder="+1 (555) 123-4567">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" id="address" name="address" value="{{ old('address', $record->address) }}"
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                                        placeholder="123 Main St, City, Country">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Details Section -->
                    <div class="form-card bg-white">
                        <div class="card-header">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                </svg>
                                Professional Details
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1 required-field">Department</label>
                                    <select id="department" name="department_id" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ $record->department_id == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1 required-field">Position</label>
                                    <select id="position" name="position_id" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                        <option value="">Select Position</option>
                                        @foreach ($positions as $position)
                                            @if ($position->department_id == $record->department_id)
                                                <option value="{{ $position->id }}" {{ $record->position_id == $position->id ? 'selected' : '' }}>
                                                    {{ $position->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1 required-field">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $record->start_date) }}" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $record->end_date) }}"
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                                    <input type="text" id="user_id" name="user_id" value="{{ old('user_id', $record->user_id) }}" readonly
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1 required-field">Employment Status</label>
                                    <select id="employment_status" name="employment_status" required
                                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                        <option value="">Select Status</option>
                                        @foreach (['Active', 'Inactive', 'Onleave'] as $status)
                                            <option value="{{ $status }}" {{ old('employment_status', $record->employment_status) == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 bg-white p-4 rounded-lg shadow-sm">
                        <a href="{{ url()->previous() }}" class="btn-secondary flex items-center justify-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary flex items-center justify-center px-6 py-2 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Department-Position relationship
            const departmentSelect = document.getElementById("department");
            const positionSelect = document.getElementById("position");
            const positions = @json($positions->groupBy('department_id'));

            function updatePositions() {
                const selectedDepartment = departmentSelect.value;
                positionSelect.innerHTML = '<option value="">Select Position</option>';

                if (selectedDepartment && positions[selectedDepartment]) {
                    positions[selectedDepartment].forEach(function(position) {
                        let option = new Option(position.name, position.id);
                        positionSelect.add(option);
                    });
                    
                    // Preselect position if it belongs to the selected department
                    const currentPositionId = "{{ $record->position_id }}";
                    if (currentPositionId) {
                        positionSelect.value = currentPositionId;
                    }
                }
            }

            departmentSelect.addEventListener("change", updatePositions);

            // Profile picture preview
            const uploadArea = document.getElementById('upload-area');
            const profileInput = document.getElementById('profile_picture');
            const profilePreview = document.getElementById('profile-preview');

            uploadArea.addEventListener('click', () => profileInput.click());
            
            profileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        if (profilePreview.tagName === 'IMG') {
                            profilePreview.src = e.target.result;
                        } else {
                            // Replace div with image
                            const img = document.createElement('img');
                            img.id = 'profile-preview';
                            img.src = e.target.result;
                            img.className = 'profile-preview rounded-full';
                            profilePreview.parentNode.replaceChild(img, profilePreview);
                        }
                    }
                    
                    reader.readAsDataURL(file);
                }
            });

            // Drag and drop for profile picture
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadArea.classList.add('drag-active');
            }

            function unhighlight() {
                uploadArea.classList.remove('drag-active');
            }

            uploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.match('image.*')) {
                        profileInput.files = files;
                        
                        // Trigger change event
                        const event = new Event('change');
                        profileInput.dispatchEvent(event);
                    } else {
                        alert('Please select an image file (JPG or PNG)');
                    }
                }
            }

            // Initialize positions based on current department
            updatePositions();
        });
    </script>
</body>
</html>