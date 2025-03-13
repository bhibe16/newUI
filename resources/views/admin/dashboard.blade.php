<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Heroicons for icons -->
    <script src="https://unpkg.com/@heroicons/v2.0.18/24/outline/index.js"></script>
</head>
<body class="main-content">
    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <!-- Pie Chart (Top-Right, 6x6) -->
    <div class="absolute top-20 right-4 w-48 h-48 group transition-transform duration-500 hover:scale-105">
        <canvas id="statusChart" class="hover:rotate-[15deg] transition-transform duration-500"></canvas>
    </div>

    <!-- Flex container to hold sidebar and main content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Welcome Banner with entrance animation -->
            <div class="bg-gradient-to-r from-yellow-100 via-yellow-200 to-yellow-300 p-10 rounded-lg shadow-lg mb-6 w-full max-w-2xl text-center transform transition-all duration-500 hover:shadow-xl animate-fade-in-up">
                <h2 class="text-4xl font-bold text-black opacity-0 animate-fade-in-down animate-delay-300">
                    Welcome {{ auth()->user()->name }}!
                </h2>
            </div>

            <!-- Statistics Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Each stat card with hover animation -->
                <a href="https://hr3.gwamerchandise.com/admin/employees/index" target="_blank">
    <div id="totalEmployeesBox" class="bg-yellow-200 p-6 rounded-lg shadow text-center transition-transform duration-300 hover:scale-105 hover:shadow-lg group cursor-pointer">
        <div class="inline-block p-3 bg-yellow-300 rounded-full transition-colors duration-300 group-hover:bg-yellow-400">
            <heroicon-outline-user-group class="w-8 h-8 text-gray-800" />
        </div>
        <h3 class="text-lg font-semibold text-black mt-3">Total Employees</h3>
        <p class="text-2xl font-bold text-gray-800 animate-count-up">{{ $totalEmployees }}</p>
    </div>
</a>

                <!-- Total Departments -->

<div id="totalDepartmentsBox" class="bg-yellow-200 p-6 rounded-lg shadow text-center transition-transform duration-300 hover:scale-105 hover:shadow-lg group cursor-pointer">
    <div class="inline-block p-3 bg-yellow-300 rounded-full transition-colors duration-300 group-hover:bg-yellow-400">
        <heroicon-outline-building-office class="w-8 h-8 text-gray-800"></heroicon-outline-building-office>
    </div>
    <h3 class="text-lg font-semibold text-black mt-3">Total Departments</h3>
    <p class="text-2xl font-bold text-gray-800 animate-count-up">{{ $totalDepartments }}</p>
</div>
<!-- Department Modal -->
<div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div id="departmentModalContent" class="bg-white p-6 rounded-lg shadow-lg w-1/3 max-h-[80vh] overflow-hidden flex flex-col">
        <h2 class="text-2xl font-bold mb-4">All Departments</h2>
        <div class="overflow-y-auto max-h-[280px] pr-2">
            <ul class="space-y-2">
                @foreach ($departments as $department)
                    <li class="p-2 border rounded bg-gray-100 whitespace-nowrap overflow-hidden text-ellipsis">
                        {{ $department->name }}
                    </li>
                @endforeach
            </ul>
        </div>
        <button id="closeModal" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Close</button>
    </div>
</div>

<!-- Total Positions -->
<div id="totalPositionsBox" class="bg-yellow-200 p-6 rounded-lg shadow text-center transition-transform duration-300 hover:scale-105 hover:shadow-lg group cursor-pointer">
    <div class="inline-block p-3 bg-yellow-300 rounded-full transition-colors duration-300 group-hover:bg-yellow-400">
        <heroicon-outline-briefcase class="w-8 h-8 text-gray-800"></heroicon-outline-briefcase>
    </div>
    <h3 class="text-lg font-semibold text-black mt-3">Total Positions</h3>
    <p class="text-2xl font-bold text-gray-800 animate-count-up">{{ $totalPositions }}</p>
</div>
<!-- Positions Modal -->
<div id="positionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div id="positionModalContent" class="bg-white p-6 rounded-lg shadow-lg w-1/3 max-h-[80vh] overflow-hidden flex flex-col">
        <h2 class="text-2xl font-bold mb-4">All Positions</h2>
        <div class="overflow-y-auto max-h-[280px] pr-2">
            <ul class="space-y-2">
                @foreach ($positions as $position)
                    <li class="p-2 border rounded bg-gray-100 whitespace-nowrap overflow-hidden text-ellipsis">
                        {{ $position->name }}
                    </li>
                @endforeach
            </ul>
        </div>
        <button id="closePositionModal" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Close</button>
    </div>
</div>
<!-- Job Post Box -->
<div id="job-box" class="absolute bottom-4 right-4 w-64 bg-white shadow-lg rounded-lg p-4 max-h-60 overflow-y-auto scrollbar-hidden">
    <!-- Job Posts Header with "View All" Link -->
    <div class="flex justify-between items-center mb-3">
        <h3 class="text-lg font-semibold">Job Posts</h3>
        <a href="https://hr3.gwamerchandise.com/admin/jobposts" target="_blank" class="text-blue-500 text-sm hover:underline">View All</a>
    </div>

    <!-- Job List -->
    <ul id="job-list" class="space-y-2">
        <li class="text-gray-500">Loading jobs...</li> <!-- Placeholder -->
    </ul>
</div>




                
                <!-- Repeat similar structure for other stats -->
                <!-- Add appropriate icons for each statistic -->
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.8s ease-out forwards;
        }

        .animate-count-up {
            animation: count-up 1s ease-out forwards;
        }

        @keyframes count-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        ::-webkit-scrollbar {
    width: 8px;
}
::-webkit-scrollbar-thumb {
    background-color: #888;
    border-radius: 4px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1;
}
/* Hide scrollbar by default */
.scrollbar-hidden::-webkit-scrollbar {
    width: 0px;
}

/* Show scrollbar on hover */
#job-box:hover::-webkit-scrollbar {
    width: 4px;
}

/* Scrollbar styling */
#job-box::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

#job-box::-webkit-scrollbar-track {
    background: transparent;
}
    </style>

<script>
    
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('statusChart').getContext('2d');
        const statusData = {!! json_encode($statusCounts) !!};

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Reject', 'Pending'],
                datasets: [{
                    label: 'Status Distribution',
                    data: [
                        statusData['approved'] || 0,
                        statusData['reject'] || 0,
                        statusData['pending'] || 0
                    ],
                    backgroundColor: ['#4CAF50', '#FF6384', '#36A2EB'],
                    borderWidth: 2,
                    borderColor: '#f3f4f6',
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 14
                            },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        bodyFont: {
                            size: 14
                        },
                        displayColors: false
                    }
                },
                onClick: function (event, elements) {
                    if (elements.length > 0) {
                        let index = elements[0].index;
                        let status = chart.data.labels[index].toLowerCase(); // Convert label to lowercase
                        
                        // Redirect to employees index page with filter
                        window.location.href = `/admin/employees/index?status=${status}`;
                    }
                }
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
    function closeModal(modalId, contentId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(contentId);

        modal.addEventListener('click', function (event) {
            if (!content.contains(event.target)) {
                modal.classList.add('hidden'); // Hide modal when clicking outside
            }
        });
    }

    // Close when clicking outside the modal content
    closeModal('departmentModal', 'departmentModalContent');
    closeModal('positionModal', 'positionModalContent');

    // Open modals
    document.getElementById('totalDepartmentsBox').addEventListener('click', function () {
        document.getElementById('departmentModal').classList.remove('hidden');
    });

    document.getElementById('totalPositionsBox').addEventListener('click', function () {
        document.getElementById('positionModal').classList.remove('hidden');
    });

    // Close modals with buttons
    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('departmentModal').classList.add('hidden');
    });

    document.getElementById('closePositionModal').addEventListener('click', function () {
        document.getElementById('positionModal').classList.add('hidden');
    });
});
document.addEventListener("DOMContentLoaded", function () {
    fetch("https://hr1.gwamerchandise.com/api/jobpost")
        .then(response => response.json()) // Convert to JSON
        .then(data => {
            const jobList = document.getElementById("job-list");
            jobList.innerHTML = ""; // Clear previous content

            data.forEach(job => {
                let listItem = document.createElement("li");
                listItem.textContent = job.title; // Extracting only the title
                listItem.classList.add("p-2", "bg-gray-100", "rounded");
                jobList.appendChild(listItem);
            });
        })
        .catch(error => {
            console.error("Error fetching job posts:", error);
            document.getElementById("job-list").innerHTML = "<li class='text-red-500'>Failed to load jobs</li>";
        });
});
</script>

</body>
</html>