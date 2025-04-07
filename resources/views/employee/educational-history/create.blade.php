<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Add Educational History</title>
    <!-- Import fonts and icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .form-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        .form-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
        }
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .conditional-field {
            transition: all 0.3s ease;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
        }
        .conditional-field.visible {
            max-height: 200px;
            opacity: 1;
            margin-top: 1rem;
        }
        .section-divider {
            position: relative;
            margin: 1.5rem 0;
        }
        .section-divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e5e7eb;
            z-index: 0;
        }
        .section-divider span {
            position: relative;
            z-index: 1;
            background-color: white;
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.875rem;
            text-transform: uppercase;
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
        <main class="flex-1 p-4 md:p-6">
            <div class="max-w-3xl mx-auto bg-white p-6 md:p-8 rounded-xl form-card">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-graduation-cap text-blue-600 mr-3"></i>
                        Add Educational History
                    </h2>
                    <p class="text-gray-600 mt-1">Fill in your academic background information</p>
                </div>

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

                <form action="{{ route('employee.educational-history.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- School Information Section -->
                    <div class="section-divider">
                        <span><i class="fas fa-school mr-1"></i> School Information</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- School Name -->
                        <div>
                            <label for="school_name" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-university text-blue-500 mr-2 text-sm"></i>
                                School Name
                            </label>
                            <input type="text" name="school_name" id="school_name" required
                                   class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input"
                                   placeholder="Enter school name"
                                   value="{{ old('school_name') }}">
                            @error('school_name')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Education Level -->
                        <div>
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-layer-group text-blue-500 mr-2 text-sm"></i>
                                Education Level
                            </label>
                            <select name="education_level" id="education_level" required
                                    class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input"
                                    onchange="toggleFields()">
                                <option value="">Select education level</option>
                                <option value="Junior High" {{ old('education_level') == 'Junior High' ? 'selected' : '' }}>Junior High</option>
                                <option value="Senior High" {{ old('education_level') == 'Senior High' ? 'selected' : '' }}>Senior High</option>
                                <option value="Tertiary" {{ old('education_level') == 'Tertiary' ? 'selected' : '' }}>Tertiary</option>
                            </select>
                            @error('education_level')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dates Section -->
                    <div class="section-divider">
                        <span><i class="far fa-calendar-alt mr-1"></i> Duration</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Start Year -->
                        <div>
                            <label for="start_year" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-calendar-day text-blue-500 mr-2 text-sm"></i>
                                Start Year
                            </label>
                            <input type="date" name="start_year" id="start_year"
                                   class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input"
                                   value="{{ old('start_year') }}">
                            @error('start_year')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Year -->
                        <div>
                            <label for="end_year" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-calendar-day text-blue-500 mr-2 text-sm"></i>
                                End Year
                            </label>
                            <input type="date" name="end_year" id="end_year"
                                   class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input"
                                   value="{{ old('end_year') }}">
                            @error('end_year')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Graduation Status -->
                    <div>
                        <label for="graduation_status" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-award text-blue-500 mr-2 text-sm"></i>
                            Graduation Status
                        </label>
                        <select name="graduation_status" id="graduation_status" required
                                class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input">
                            <option value="">Select graduation status</option>
                            <option value="Completed" {{ old('graduation_status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Not Completed" {{ old('graduation_status') == 'Not Completed' ? 'selected' : '' }}>Not Completed</option>
                        </select>
                        @error('graduation_status')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Track/Strand (Only for Senior High) -->
                    <div id="track_strand_field" class="conditional-field {{ old('education_level') == 'Senior High' ? 'visible' : '' }}">
                        <label for="track_strand" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-road text-blue-500 mr-2 text-sm"></i>
                            Track/Strand (For Senior High)
                        </label>
                        <input type="text" name="track_strand" id="track_strand"
                               class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input"
                               placeholder="Enter track or strand (e.g., STEM, ABM, HUMSS)"
                               value="{{ old('track_strand') }}">
                        @error('track_strand')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Program (Only for Tertiary) -->
                    <div id="program_field" class="conditional-field {{ old('education_level') == 'Tertiary' ? 'visible' : '' }}">
                        <label for="program" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-book text-blue-500 mr-2 text-sm"></i>
                            Program (For College)
                        </label>
                        <input type="text" name="program" id="program"
                               class="mt-1 block w-full p-3 border border-gray-300 rounded-lg form-input"
                               placeholder="Enter your program or course (e.g., BS Computer Science)"
                               value="{{ old('program') }}">
                        @error('program')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('employee.records.index') }}" 
                           class="px-6 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition duration-200 text-center">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-200 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Save Education
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleFields() {
            const educationLevel = document.getElementById('education_level').value;
            const trackStrandField = document.getElementById('track_strand_field');
            const programField = document.getElementById('program_field');

            // Reset both fields
            trackStrandField.classList.remove('visible');
            programField.classList.remove('visible');

            // Show appropriate field based on selection
            if (educationLevel === 'Senior High') {
                trackStrandField.classList.add('visible');
            } else if (educationLevel === 'Tertiary') {
                programField.classList.add('visible');
            }
        }

        // Initialize fields on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
        });
    </script>
</body>
</html>