<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Create Employee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .form-section {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .form-section:hover {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .form-header {
            color: #1e40af;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .required-field::after {
            content: " *";
            color: #ef4444;
        }
        .profile-picture-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            overflow: hidden;
        }
        .file-upload-label {
            transition: all 0.3s ease;
        }
        .file-upload-label:hover {
            transform: translateY(-2px);
        }
        .help-text {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="main-content min-h-screen bg-gray-50">
    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 md:p-8">
            <div class="bg-white p-6 rounded-xl shadow-sm text-gray-900 max-w-5xl mx-auto">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Create Employee Record</h1>
                    <p class="text-gray-600 mt-2">Fill in the details below to create a new employee record</p>
                </div>

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
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

                <!-- Employee Record Form -->
                <form action="{{ route('employee.records.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Profile Picture Section -->
                    <div class="form-section">
                        <h2 class="form-header text-lg font-semibold">Profile Picture</h2>
                        <div class="flex flex-col items-center space-y-4">
                            <div class="profile-picture-preview" id="profilePicturePreview">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <label for="profile_picture" class="file-upload-label cursor-pointer px-4 py-2 bg-white border border-blue-500 rounded-md shadow-sm text-sm font-medium text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Choose Photo
                                <input type="file" id="profile_picture" name="profile_picture" class="sr-only" accept="image/*">
                            </label>
                            <p class="help-text">JPG or PNG, max 2MB</p>
                        </div>
                    </div>

                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h2 class="form-header text-lg font-semibold">Personal Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 required-field">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 required-field">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 required-field">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 required-field">Gender</label>
                                <select id="gender" name="gender" 
                                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Gender</option>
                                    @foreach (['Male', 'Female', 'Other'] as $gender)
                                        <option value="{{ $gender }}" {{ old('gender') == $gender ? 'selected' : '' }}>{{ $gender }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="marital_status" class="block text-sm font-medium text-gray-700 required-field">Marital Status</label>
                                <select id="marital_status" name="marital_status" 
                                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Status</option>
                                    @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                        <option value="{{ $status }}" {{ old('marital_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nationality" class="block text-sm font-medium text-gray-700 required-field">Nationality</label>
                                <input type="text" id="nationality" name="nationality" value="{{ old('nationality') }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <h2 class="form-header text-lg font-semibold">Contact Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed focus:ring-blue-500 focus:border-blue-500" readonly>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phoneNumber) }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed focus:ring-blue-500 focus:border-blue-500" readonly>
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 required-field">Address</label>
                                <input type="text" id="address" name="address" value="{{ old('address', auth()->user()->address) }}"  
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Section -->
<div class="form-section">
    <h2 class="form-header text-lg font-semibold">Emergency Contact</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 required-field">Full Name</label>
            <input type="text" id="emergency_contact_name" name="emergency_contact[name]" value="{{ old('emergency_contact.name') }}" 
                   class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 required-field">Relationship</label>
            <input type="text" id="emergency_contact_relationship" name="emergency_contact[relationship]" value="{{ old('emergency_contact.relationship') }}" 
                   class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 required-field">Phone</label>
            <input type="text" id="emergency_contact_phone" name="emergency_contact[phone]" value="{{ old('emergency_contact.phone') }}" 
                   class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="emergency_contact_email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="emergency_contact_email" name="emergency_contact[email]" value="{{ old('emergency_contact.email') }}" 
                   class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="md:col-span-2">
            <label for="emergency_contact_address" class="block text-sm font-medium text-gray-700">Address</label>
            <input type="text" id="emergency_contact_address" name="emergency_contact[address]" value="{{ old('emergency_contact.address') }}" 
                   class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
</div>

                    <!-- Professional Details Section -->
                    <div class="form-section">
                        <h2 class="form-header text-lg font-semibold">Professional Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                                <select id="department" name="department" 
                                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                                <select id="position" name="position" 
                                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Position</option>
                                </select>
                            </div>
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 required-field">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="help-text">Leave blank if currently employed</p>
                            </div>
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                                <input type="text" id="user_id" name="user_id" value="{{ old('user_id', auth()->user()->user_id) }}" 
                                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed focus:border-blue-500" readonly>
                            </div>
                            <div>
    <label for="employment_status" class="block text-sm font-medium text-gray-700">Employment Status</label>
    <select id="employment_status" name="employment_status" 
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        <option value="Active" {{ old('employment_status', auth()->user()->status) == 'Active' ? 'selected' : '' }}>Active</option>
        <option value="Inactive" {{ old('employment_status', auth()->user()->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
        <option value="Onleave" {{ old('employment_status', auth()->user()->status) == 'Onleave' ? 'selected' : '' }}>Onleave</option>
    </select>
</div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Employee Record
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
        const positions = @json($departments->mapWithKeys(fn($dept) => [$dept->id => $dept->positions->map(fn($pos) => ['id' => $pos->id, 'name' => $pos->name])]));

        departmentSelect.addEventListener("change", function() {
            const selectedDepartment = this.value;
            positionSelect.innerHTML = '<option value="">Select Position</option>';

            if (positions[selectedDepartment]) {
                positions[selectedDepartment].forEach(function(position) {
                    let option = new Option(position.name, position.id);
                    positionSelect.add(option);
                });
            }
        });

        // Profile picture preview
        const profilePictureInput = document.getElementById('profile_picture');
        const profilePicturePreview = document.getElementById('profilePicturePreview');

        profilePictureInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    profilePicturePreview.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-cover';
                    profilePicturePreview.appendChild(img);
                }
                
                reader.readAsDataURL(file);
            }
        });

        // Initialize date inputs with today's date if empty
        const today = new Date().toISOString().split('T')[0];
        if (!document.getElementById('start_date').value) {
            document.getElementById('start_date').value = today;
        }
    });
    </script>
</body>
</html>