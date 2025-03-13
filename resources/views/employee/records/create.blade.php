<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="main-content min-h-screen">
    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-16">
            <div class="bg-white p-6 rounded-lg shadow-lg text-gray-900 max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold mb-6 text-center">Create Employee Record</h1>

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 rounded-md mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Employee Record Form -->
                <form action="{{ route('employee.records.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <!-- Profile Picture Upload -->
                    <div class="flex flex-col items-center space-y-4">
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" class="block w-full max-w-xs p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Personal Information Section -->
                    <div>
                        <h2 class="text-xl font-bold mb-4">Personal Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="middle_name" class="block text-sm font-medium">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium">Gender</label>
                                <select id="gender" name="gender" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                    <option value="">Select Gender</option>
                                    @foreach (['Male', 'Female'] as $gender)
                                        <option value="{{ $gender }}" {{ old('gender') == $gender ? 'selected' : '' }}>{{ $gender }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="marital_status" class="block text-sm font-medium">Marital Status</label>
                                <input type="text" id="marital_status" name="marital_status" value="{{ old('marital_status') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="nationality" class="block text-sm font-medium">Nationality</label>
                                <input type="text" id="nationality" name="nationality" value="{{ old('nationality') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div>
                        <h2 class="text-xl font-bold mb-4">Contact Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" readonly>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium">Phone</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phoneNumber) }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" readonly>
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium">Address</label>
                                <input type="text" id="address" name="address" value="{{ old('address', auth()->user()->address) }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Details Section -->
                    <div>
                        <h2 class="text-xl font-bold mb-4">Professional Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="department" class="block text-sm font-medium">Department</label>
                                <select id="department" name="department" class="form-select mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="position" class="block text-sm font-medium">Position</label>
                                <select id="position" name="position" class="form-select mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select Position</option>
                                </select>
                            </div>
                            <div>
                                <label for="start_date" class="block text-sm font-medium">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="user_id" class="block text-sm font-medium">Employee ID</label>
                                <input type="text" id="user_id" name="user_id" value="{{ old('user_id', auth()->user()->user_id) }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" readonly>
                            </div>
                            <div>
                                <label for="employment_status" class="block text-sm font-medium">Employment Status</label>
                                <select id="employment_status" name="employment_status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                    <option value="">Select Status</option>
                                    @foreach (['Active ', 'Inactive'] as $employment_status)
                                        <option value="{{ $employment_status }}" {{ old('employment_status') == $employment_status ? 'selected' : '' }}>{{ $employment_status }}</option>
                                    @endforeach
                                </select>
                            </div>  
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:ring-2 focus:ring-blue-300">Create Record</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
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
    });
</script>

</body>
</html>
