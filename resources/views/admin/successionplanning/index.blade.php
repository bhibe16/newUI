<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Succession Planning</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="main-content min-h-screen">
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <main class="flex-grow p-16">
            <div class="container mx-auto p-6">
                <h2 class="text-2xl font-bold mb-4">Succession Planning</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($successionplanning as $succession)
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6 border border-gray-200">
                            <p class="text-gray-600"><span class="font-semibold">ID:</span> {{ $succession['id'] }}</p>
                            <p class="text-gray-600"><span class="font-semibold">Name:</span> {{ $succession['first_name'] }} {{ $succession['last_name'] }}</p>
                            <p class="text-gray-600"><span class="font-semibold">Current Position:</span></p>
                            <p class="text-gray-600"><span class="font-semibold">Department:</span></p> 
                            <p class="text-gray-600"><span class="font-semibold">Years of Experience:</span></p>
                            <p class="text-gray-600"><span class="font-semibold">Final Score:</span> {{ $succession['final_score'] }}</p>
                            <p class="text-gray-600"><span class="font-semibold">Status:</span> 
                                <span class="px-2 py-1 rounded text-white 
                                    {{ $succession['status'] === 'Pass' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ $succession['status'] }}
                                </span>
                            </p>
                            <p class="text-gray-600 font-semibold mt-2">
                                @if ($succession['status'] === 'Pass')
                                    ✅ Eligible for Promotion
                                    <p class="text-gray-600"><span class="font-semibold">Potential Future Role:</span></p>
                                @else
                                    ❌ Needs Improvement - Consider Additional Training
                                    <p class="text-gray-600"><span class="font-semibold">Recommended Training:</span></p>
                                @endif
                            </p>
                            <div class="mt-4 flex justify-between">
                                <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">Assign Mentor</button>
                                <button class="bg-gray-500 text-white px-4 py-2 rounded-lg">Download Report</button>
                            </div>
                        </div>
                    @endforeach 
                </div>
            </div>
        </main>
    </div>
</body>
</html>