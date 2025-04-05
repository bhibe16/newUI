<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Heroicons for icons -->
    <script src="https://unpkg.com/@heroicons/v2.0.18/24/outline/index.js"></script>
    <!-- Font Awesome for additional icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="main-content bg-gray-50">
    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <!-- Main Content Layout -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 p-6">
            <!-- Dashboard Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-yellow-100 via-yellow-200 to-orange-300 p-6 rounded-xl shadow-md w-full max-w-3xl animate-fade-in-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 opacity-0 animate-fade-in-down animate-delay-300">
                                Welcome back, {{ auth()->user()->name }}!
                            </h2>
                            <p class="text-gray-700 mt-2">Here's what's happening with your HRIS today.</p>
                        </div>
                        <div class="hidden md:block">
                            <i class="fas fa-user-shield text-4xl text-gray-700 opacity-20"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Summary Chart -->
                <div class="w-full md:w-64 bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-yellow-500"></i> Status Summary
                    </h3>
                    <div class="h-40">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Employee Users -->
                <a href="" class="transform transition-all duration-300 hover:scale-[1.02]">
                    <div class="bg-white p-5 rounded-xl shadow-md hover:shadow-lg group cursor-pointer flex justify-between items-center border-l-4 border-yellow-400">
                        <div class="text-left">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Employee Users</h3>
                            <p class="text-2xl font-bold text-gray-800 mt-1 animate-count-up">{{ $employeeUsers }}</p>
                            <p class="text-xs text-gray-500 mt-1">Active accounts</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-xl text-yellow-600 transition-colors duration-300 group-hover:bg-yellow-200">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Total Employees -->
                <a href="https://hr3.gwamerchandise.com/admin/employees" target="_blank" class="transform transition-all duration-300 hover:scale-[1.02]">
                    <div class="bg-white p-5 rounded-xl shadow-md hover:shadow-lg group cursor-pointer flex justify-between items-center border-l-4 border-blue-400">
                        <div class="text-left">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Employees</h3>
                            <p class="text-2xl font-bold text-gray-800 mt-1 animate-count-up">{{ $totalEmployees }}</p>
                            <p class="text-xs text-gray-500 mt-1">All team members</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-xl text-blue-600 transition-colors duration-300 group-hover:bg-blue-200">
                            <i class="fas fa-id-card-alt text-xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Total Departments -->
                <div onclick="openModal('departmentModal')" class="transform transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                    <div class="bg-white p-5 rounded-xl shadow-md hover:shadow-lg group flex justify-between items-center border-l-4 border-green-400">
                        <div class="text-left">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Departments</h3>
                            <p class="text-2xl font-bold text-gray-800 mt-1 animate-count-up">{{ $totalDepartments }}</p>
                            <p class="text-xs text-gray-500 mt-1">Click to view all</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-xl text-green-600 transition-colors duration-300 group-hover:bg-green-200">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Positions -->
                <div onclick="openModal('positionModal')" class="transform transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                    <div class="bg-white p-5 rounded-xl shadow-md hover:shadow-lg group flex justify-between items-center border-l-4 border-purple-400">
                        <div class="text-left">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Positions</h3>
                            <p class="text-2xl font-bold text-gray-800 mt-1 animate-count-up">{{ $totalPositions }}</p>
                            <p class="text-xs text-gray-500 mt-1">Click to view all</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-xl text-purple-600 transition-colors duration-300 group-hover:bg-purple-200">
                            <i class="fas fa-briefcase text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- New Hires Chart -->
                <div class="bg-white p-6 rounded-xl shadow-md animate-fade-in-up">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-user-plus text-yellow-500"></i> New Hires (Last 6 Months)
                        </h3>
                        <div class="flex gap-2">
                            <button class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded" onclick="updateChartTimeframe('6')">6M</button>
                            <button class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded" onclick="updateChartTimeframe('12')">12M</button>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="newHiresChart"></canvas>
                    </div>
                </div>

                <!-- Job Posts Widget -->
                <div class="bg-white p-6 rounded-xl shadow-md animate-fade-in-up animate-delay-300">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-bullhorn text-blue-500"></i> Recent Job Posts
                        </h3>
                        <a href="https://hr3.gwamerchandise.com/admin/jobposts" target="_blank" class="text-sm text-blue-500 hover:underline">View All</a>
                    </div>
                    <div class="h-64 overflow-y-auto pr-2 custom-scrollbar">
                        <ul id="job-list" class="space-y-3">
                            <li class="flex items-center justify-center h-full">
                                <div class="animate-pulse flex space-x-4">
                                    <div class="flex-1 space-y-4 py-1">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="space-y-2">
                                            <div class="h-4 bg-gray-200 rounded"></div>
                                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Modal -->
    <div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">All Departments</h2>
                    <button onclick="closeModal('departmentModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="relative mt-4">
                    <input type="text" id="deptSearch" placeholder="Search departments..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-300">
                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div class="overflow-y-auto px-6 py-4">
                <ul id="departmentList" class="space-y-2">
                    @foreach ($departments as $department)
                        <li class="p-3 border rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex justify-between items-center">
                                <span class="font-medium">{{ $department->name }}</span>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $department->employees_count ?? 0 }} members</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="p-4 border-t">
                <button onclick="closeModal('departmentModal')" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Positions Modal -->
    <div id="positionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">All Positions</h2>
                    <button onclick="closeModal('positionModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="relative mt-4">
                    <input type="text" id="positionSearch" placeholder="Search positions..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-300">
                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div class="overflow-y-auto px-6 py-4">
                <ul id="positionList" class="space-y-2">
                    @foreach ($positions as $position)
                        <li class="p-3 border rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex justify-between items-center">
                                <span class="font-medium">{{ $position->name }}</span>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $position->employees_count ?? 0 }} employees</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="p-4 border-t">
                <button onclick="closeModal('positionModal')" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg transition-colors duration-200">
                    Close
                </button>
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
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background-color: rgba(156, 163, 175, 0.1);
        }

        /* Smooth transitions for hover effects */
        .transition-bg {
            transition: background-color 0.3s ease;
        }
    </style>

    <script>
        // Global chart variables
        let hiresChart;
        let statusChart;
        let currentTimeframe = 6;

        document.addEventListener("DOMContentLoaded", function () {
            // Initialize New Hires Chart
            initHiresChart();
            
            // Initialize Status Chart
            initStatusChart();
            
            // Load job posts
            loadJobPosts();
            
            // Setup modal search functionality
            setupSearch('deptSearch', 'departmentList');
            setupSearch('positionSearch', 'positionList');
        });

        function initHiresChart() {
            const hiresCtx = document.getElementById('newHiresChart').getContext('2d');
            hiresChart = new Chart(hiresCtx, {
                type: 'line',
                data: {
                    labels: @json($monthNames),
                    datasets: [{
                        label: 'New Hires',
                        data: @json($monthlyHires),
                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                        borderColor: 'rgba(234, 179, 8, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(234, 179, 8, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.y} new hire${context.parsed.y !== 1 ? 's' : ''}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        function initStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            const statusData = {!! json_encode($statusCounts) !!};

            statusChart = new Chart(ctx, {
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
                        backgroundColor: ['#10B981', '#EF4444', '#3B82F6'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 10,
                        weight: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12,
                                    family: "'Inter', sans-serif"
                                },
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            bodyFont: {
                                size: 14
                            },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    onClick: function (event, elements) {
                        if (elements.length > 0) {
                            let index = elements[0].index;
                            let status = this.data.labels[index].toLowerCase();
                            window.location.href = `/admin/employees/index?status=${status}`;
                        }
                    }
                }
            });
        }

        function updateChartTimeframe(months) {
            currentTimeframe = parseInt(months);
            // Here you would typically fetch new data based on the timeframe
            // For now, we'll just update the chart title
            document.querySelector('#newHiresChart').closest('div').querySelector('h3').textContent = 
                `New Hires (Last ${months} Months)`;
            
            // In a real implementation, you would fetch new data from the server:
            // fetch(`/api/new-hires?months=${months}`)
            //     .then(response => response.json())
            //     .then(data => {
            //         hiresChart.data.labels = data.monthNames;
            //         hiresChart.data.datasets[0].data = data.monthlyHires;
            //         hiresChart.update();
            //     });
        }

        function loadJobPosts() {
            const jobList = document.getElementById("job-list");
            
            // Show loading state
            jobList.innerHTML = `
                <li class="flex items-center justify-center h-48">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500">Loading job posts...</p>
                    </div>
                </li>
            `;
            
            fetch("https://hr1.gwamerchandise.com/api/jobpost")
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        jobList.innerHTML = `
                            <li class="flex items-center justify-center h-48">
                                <div class="text-center">
                                    <i class="fas fa-briefcase text-2xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500">No active job posts</p>
                                </div>
                            </li>
                        `;
                        return;
                    }
                    
                    jobList.innerHTML = "";
                    data.slice(0, 5).forEach(job => {
                        const listItem = document.createElement("li");
                        listItem.className = "p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200";
                        
                        listItem.innerHTML = `
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-800">${job.title}</h4>
                                    <p class="text-xs text-gray-500 mt-1">${job.department || 'No department specified'}</p>
                                </div>
                                <span class="text-xs bg-${job.status === 'open' ? 'green' : 'red'}-100 text-${job.status === 'open' ? 'green' : 'red'}-600 px-2 py-1 rounded">
                                    ${job.status === 'open' ? 'Active' : 'Closed'}
                                </span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500">
                                    <i class="far fa-calendar-alt mr-1"></i> 
                                    ${new Date(job.created_at).toLocaleDateString()}
                                </span>
                                <a href="https://hr3.gwamerchandise.com/admin/jobposts/${job.id}" target="_blank" class="text-xs text-blue-500 hover:underline">
                                    View <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            </div>
                        `;
                        jobList.appendChild(listItem);
                    });
                })
                .catch(error => {
                    console.error("Error fetching job posts:", error);
                    jobList.innerHTML = `
                        <li class="flex items-center justify-center h-48">
                            <div class="text-center text-red-500">
                                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                <p>Failed to load job posts</p>
                                <p class="text-xs mt-1">Please try again later</p>
                            </div>
                        </li>
                    `;
                });
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = '';
        }

        function setupSearch(inputId, listId) {
            const searchInput = document.getElementById(inputId);
            const list = document.getElementById(listId);
            
            if (!searchInput || !list) return;
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const items = list.getElementsByTagName('li');
                
                Array.from(items).forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.id === 'departmentModal') {
                closeModal('departmentModal');
            }
            if (event.target.id === 'positionModal') {
                closeModal('positionModal');
            }
        });
    </script>
</body>
</html>