<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS</title>
     @production
    <link rel="stylesheet" href="{{ asset('build/assets/style-qeVbSJLa.css') }}">
    <script type="module" src="{{ asset('build/assets/app-LM_T2gVS.js') }}"></script>
@else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endproduction
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
   <style>
        .profile-card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 10; /* Lower than dropdown */
        }
        .section-title {
            color: #4b5563;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }
        .info-label {
            color: #6b7280;
            width: 140px;
            display: inline-block;
        }
        .edit-btn {
            transition: all 0.2s ease;
        }
        .edit-btn:hover {
            transform: rotate(10deg);
            background-color: #f59e0b !important;
        }
        .divider-line {
            border-color: #e5e7eb;
            border-style: solid;
            border-width: 0;
            background-image: linear-gradient(to right, transparent, #d1d5db, transparent);
            height: 1px;
        }
        [x-cloak] { display: none !important; }
        
        /* New styles for dropdown fix */
        .nav-container {
            position: relative;
            z-index: 1000;
        }
        .user-dropdown {
            z-index: 1001 !important;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navigation Bar -->
    <div class="nav-container">
        @include('layouts.navigation')
    </div>

    <div class="flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 md:p-8">

            <!-- Employee Record Section -->
            <div class="mb-6">
                @forelse ($record as $empRecord)
                <div class="profile-card p-6 relative">
                    <!-- Header with title and edit button -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Employee Profile
                        </h2>
                        <!-- Edit dropdown -->
<div x-data="{ open: false }" class="relative" x-cloak>
    <button @click.stop="open = !open" class="edit-btn p-2 rounded-full bg-gray-100 hover:bg-yellow-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
    </button>
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200 origin-top-right">
        <a href="{{ route('employee.records.edit', $empRecord->id) }}" 
           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Profile
        </a>
        
    </div>
</div>
                    </div>

                    <!-- Profile content -->
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Left side - Profile image and basic info -->
                        <div class="flex flex-col items-center md:items-start md:w-1/3">
                            <img src="{{ !empty($empRecord->profile_picture) ? asset('storage/' . $empRecord->profile_picture) : asset('storage/default.png') }}" 
                                alt="Profile Picture" 
                                class="w-32 h-32 rounded-full border-4 border-white shadow-lg mb-4">
                            
                            <div class="text-center md:text-left">
                                <h3 class="text-xl font-semibold text-gray-800">{{ $empRecord->first_name ?? 'N/A' }} {{ $empRecord->last_name ?? '' }}</h3>
                                <p class="text-blue-600 font-medium">{{ $empRecord->position->name ?? 'N/A' }}</p>
                                <p class="text-gray-600">{{ $empRecord->department->name ?? 'N/A' }}</p>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="font-medium">ID:</span> {{ $empRecord->user_id ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <!-- Vertical divider - hidden on mobile -->
                        <div class="hidden md:block border-l border-gray-200 mx-2"></div>

                        <!-- Right side - Personal info -->
                        <div class="md:w-2/3">
                            <h3 class="section-title mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><span class="info-label">First Name:</span> {{ $empRecord->first_name ?? 'N/A' }}</p>
                                    <p><span class="info-label">Middle Name:</span> {{ $empRecord->middle_name ?? 'N/A' }}</p>
                                    <p><span class="info-label">Last Name:</span> {{ $empRecord->last_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p><span class="info-label">Date of Birth:</span> 
                                        {{ !empty($empRecord->date_of_birth) ? \Carbon\Carbon::parse($empRecord->date_of_birth)->format('m/d/Y') : 'N/A' }}
                                    </p>
                                    <p><span class="info-label">Gender:</span> {{ $empRecord->gender ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Empty state -->
                <div class="profile-card p-6 relative">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-700">Employee Profile</h2>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="edit-btn p-2 rounded-full bg-gray-100 hover:bg-yellow-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                <a href="{{ route('employee.records.create') }}" 
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create Profile
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-600">No employee profile found</h3>
                        <p class="mt-1 text-gray-500">Create a new profile to get started</p>
                        <a href="{{ route('employee.records.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            Create Profile
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Contact & Professional Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @forelse ($record as $empRecord)
                    <!-- Contact Information -->
                    <div class="profile-card p-6">
                        <h3 class="section-title flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact Information
                        </h3>
                        <div class="mt-4 space-y-3">
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="info-label">Email:</span> {{ $empRecord->email ?? 'N/A' }}
                            </p>
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="info-label">Phone:</span> {{ $empRecord->phone ?? 'N/A' }}
                            </p>
                            <p class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>
                                    <span class="info-label">Address:</span> 
                                    <span class="text-gray-700">{{ $empRecord->address ?? 'N/A' }}</span>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Professional Details -->
                    <div class="profile-card p-6">
                        <h3 class="section-title flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Professional Details
                        </h3>
                        <div class="mt-4 space-y-3">
                            <p>
                                <span class="info-label">Department:</span> 
                                <span class="text-gray-700">{{ $empRecord->department->name ?? 'N/A' }}</span>
                            </p>
                            <p>
                                <span class="info-label">Position:</span> 
                                <span class="text-gray-700">{{ $empRecord->position->name ?? 'N/A' }}</span>
                            </p>
                            <p>
                                <span class="info-label">Employment Status:</span> 
                                <span class="text-gray-700">{{ $empRecord->employment_status ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>
                @empty
                    <!-- Empty state for contact info -->
                    <div class="profile-card p-6">
                        <h3 class="section-title">Contact Information</h3>
                        <div class="text-center py-8 text-gray-500">
                            No contact information available
                        </div>
                    </div>

                    <!-- Empty state for professional details -->
                    <div class="profile-card p-6">
                        <h3 class="section-title">Professional Details</h3>
                        <div class="text-center py-8 text-gray-500">
                            No professional details available
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Education and Work Experience -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Educational History -->
                <div class="profile-card p-6" x-data="{ openEducationModal: false }">
                    <div class="flex justify-between items-center">
                        <h3 class="section-title flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                            </svg>
                            Education
                        </h3>
                        <button @click="openEducationModal = true" class="edit-btn p-2 rounded-full bg-gray-100 hover:bg-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4 space-y-4">
                        @php
                            $orderedEducation = $educationalHistory->sortBy(function ($education) {
                                return match ($education->education_level) {
                                    'Junior High' => 1,
                                    'Senior High' => 2,
                                    'Tertiary' => 3,
                                    default => 4,
                                };
                            });
                        @endphp

                        @forelse ($orderedEducation as $education)
                        <div class="pl-4 border-l-2 border-blue-200">
                            <h4 class="font-medium text-gray-800">{{ $education->education_level ?? 'N/A' }}</h4>
                            <p class="text-gray-600">{{ $education->school_name ?? 'N/A' }}</p>
                            @if($education->education_level === 'Senior High' && !empty($education->track_strand))
                                <p class="text-sm text-gray-500">{{ $education->track_strand }}</p>
                            @endif
                            @if($education->education_level === 'Tertiary' && !empty($education->program))
                                <p class="text-sm text-gray-500">{{ $education->program }}</p>
                            @endif
                            <p class="text-sm text-gray-500">
                                {{ $education->start_year ? \Carbon\Carbon::parse($education->start_year)->format('Y') : 'N/A' }} - 
                                {{ $education->end_year ? \Carbon\Carbon::parse($education->end_year)->format('Y') : 'Present' }}
                            </p>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500">
                            No educational background added
                        </div>
                        @endforelse
                    </div>

                    <!-- Education Modal -->
                    <div x-show="openEducationModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[80vh] flex flex-col">
                            <div class="p-6 overflow-y-auto">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Manage Education</h3>
                                
                                <ul class="divide-y divide-gray-200 mb-6">
                                    @foreach ($educationalHistory as $education)
                                    <li class="py-3">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-medium">{{ $education->school_name }}</p>
                                                <p class="text-sm text-gray-500">{{ $education->education_level }}</p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('employee.educational-history.edit', $education->id) }}" 
                                                    class="p-1 text-blue-500 hover:text-blue-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('employee.educational-history.destroy', $education->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure?')" 
                                                        class="p-1 text-red-500 hover:text-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if($educationalHistory->count() < 3)
                            <div class="px-6 pb-4">
                                <a href="{{ route('employee.educational-history.create') }}" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-black rounded-md hover:bg-green-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Education
                                </a>
                            </div>
                            @endif
                            
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button @click="openEducationModal = false" type="button" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Experience -->
                <div class="profile-card p-6" x-data="{ openWorkModal: false }">
                    <div class="flex justify-between items-center">
                        <h3 class="section-title flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Work Experience
                        </h3>
                        <button @click="openWorkModal = true" class="edit-btn p-2 rounded-full bg-gray-100 hover:bg-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4 space-y-4">
                        @forelse ($history as $empHistory)
                        <div class="pl-4 border-l-2 border-blue-200">
                            <h4 class="font-medium text-gray-800">{{ $empHistory->position ?? 'N/A' }}</h4>
                            <p class="text-gray-600">{{ $empHistory->company_name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">
                                @php
                                    $startDate = $empHistory->start_date ? \Carbon\Carbon::parse($empHistory->start_date) : null;
                                    $endDate = $empHistory->end_date ? \Carbon\Carbon::parse($empHistory->end_date) : null;
                                    $startFormatted = $startDate ? $startDate->format('M Y') : 'N/A';
                                    $endFormatted = $endDate ? $endDate->format('M Y') : 'Present';
                                @endphp
                                {{ $startFormatted }} - {{ $endFormatted }}
                            </p>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500">
                            No work experience added
                        </div>
                        @endforelse
                    </div>

                    <!-- Work Experience Modal -->
                    <div x-show="openWorkModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[80vh] flex flex-col">
                            <div class="p-6 overflow-y-auto">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Manage Work Experience</h3>
                                
                                <ul class="divide-y divide-gray-200 mb-6">
                                    @foreach ($history as $empHistory)
                                    <li class="py-3">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-medium">{{ $empHistory->position }}</p>
                                                <p class="text-sm text-gray-500">{{ $empHistory->company_name }}</p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('employee.history.edit', $empHistory->id) }}" 
                                                    class="p-1 text-blue-500 hover:text-blue-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('employee.history.destroy', $empHistory->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure?')" 
                                                        class="p-1 text-red-500 hover:text-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if($history->count() < 3)
                            <div class="px-6 pb-4">
                                <a href="{{ route('employee.history.create') }}" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-black rounded-md hover:bg-green-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Experience
                                </a>
                            </div>
                            @endif
                            
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button @click="openWorkModal = false" type="button" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js initialized successfully');
    });
</script>
</body>
</html>