<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIS - Job Lists</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .job-card {
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .status-published {
            background-color: #e6f7ee;
            color: #10b981;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #f59e0b;
        }
        .status-private {
            background-color: #e5e7eb;
            color: #6b7280;
        }
        .modal-enter {
            animation: modal-enter 0.3s ease-out;
        }
        @keyframes modal-enter {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .tab-active {
            border-bottom: 2px solid #3b82f6;
            color: #3b82f6;
            font-weight: 500;
        }
    </style>
</head>
<body class="min-h-screen">
@include('layouts.navigation')
    
    <div class="flex">
    @include('layouts.sidebar')
        
        <main class="flex-1 p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Job Lists</h1>
                        <p class="text-gray-600">Find your next career opportunity</p>
                    </div>
                    <div class="relative w-full md:w-64">
                        <input type="text" id="searchInput" placeholder="Search jobs..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search text-gray-400 absolute left-3 top-3"></i>
                    </div>
                </div>
                
                <!-- Status Tabs -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button class="tab-btn px-4 py-2 mr-2 tab-active" data-status="all">All Jobs</button>
                    <button class="tab-btn px-4 py-2 mr-2" data-status="published">Published</button>
                    <button class="tab-btn px-4 py-2 mr-2" data-status="pending">Pending</button>
                    <button class="tab-btn px-4 py-2" data-status="private">Private</button>
                </div>
                
                <!-- Filter Chips -->
                <div class="mb-6 flex flex-wrap gap-2">
                    <button class="filter-chip px-3 py-1 bg-white border border-gray-300 rounded-full text-sm font-medium" data-filter="all">All</button>
                    <button class="filter-chip px-3 py-1 bg-white border border-gray-300 rounded-full text-sm font-medium" data-filter="IT Department">IT</button>
                    <button class="filter-chip px-3 py-1 bg-white border border-gray-300 rounded-full text-sm font-medium" data-filter="Sales Department">Sales</button>
                    <button class="filter-chip px-3 py-1 bg-white border border-gray-300 rounded-full text-sm font-medium" data-filter="Full-Time">Full-Time</button>
                    <button class="filter-chip px-3 py-1 bg-white border border-gray-300 rounded-full text-sm font-medium" data-filter="Part-Time">Part-Time</button>
                    <button class="filter-chip px-3 py-1 bg-white border border-gray-300 rounded-full text-sm font-medium" data-filter="WFH">WFH</button>
                </div>
                
                <!-- Job Cards Container -->
                <div id="jobContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Jobs will be loaded here dynamically -->
                </div>
                
                <!-- Loading State -->
                <div id="loadingIndicator" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                    <p class="mt-2 text-gray-600">Loading jobs...</p>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="hidden text-center py-12">
                    <i class="fas fa-briefcase text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700">No jobs found</h3>
                    <p class="text-gray-500 mt-1">Try adjusting your search or filters</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Job Detail Modal -->
    <div id="jobModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto modal-enter">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 id="modalTitle" class="text-2xl font-bold text-gray-800"></h2>
                        <div class="flex items-center mt-2">
                            <span id="modalDept" class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2"></span>
                            <span id="modalLocation" class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                            </span>
                        </div>
                    </div>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Salary</h3>
                        <p id="modalSalary" class="font-medium"></p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Schedule</h3>
                        <p id="modalSchedule" class="font-medium"></p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</h3>
                        <p id="modalStatus" class="font-medium"></p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Job Description</h3>
                    <div id="modalDescription" class="text-gray-700 whitespace-pre-line"></div>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Requirements</h3>
                    <div id="modalRequirements" class="text-gray-700 whitespace-pre-line"></div>
                </div>
                
                <div class="border-t pt-4 text-sm text-gray-500">
                    <div class="flex justify-between">
                        <span id="modalCreatedAt"></span>
                        <span id="modalUpdatedAt"></span>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition">
                        Apply Now
                    </button>
                    <button onclick="closeModal()" class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-2 px-4 rounded-lg font-medium transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const jobContainer = document.getElementById('jobContainer');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const emptyState = document.getElementById('emptyState');
        const searchInput = document.getElementById('searchInput');
        const tabButtons = document.querySelectorAll('.tab-btn');
        const filterChips = document.querySelectorAll('.filter-chip');
        
        // Modal Elements
        const jobModal = document.getElementById('jobModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalDept = document.getElementById('modalDept');
        const modalLocation = document.getElementById('modalLocation');
        const modalSalary = document.getElementById('modalSalary');
        const modalSchedule = document.getElementById('modalSchedule');
        const modalStatus = document.getElementById('modalStatus');
        const modalDescription = document.getElementById('modalDescription');
        const modalRequirements = document.getElementById('modalRequirements');
        const modalCreatedAt = document.getElementById('modalCreatedAt');
        const modalUpdatedAt = document.getElementById('modalUpdatedAt');
        
        // State
        let allJobs = [];
        let filteredJobs = [];
        let currentStatusFilter = 'all';
        let currentDepartmentFilter = 'all';
        let currentScheduleFilter = 'all';
        let currentSearchTerm = '';
        
        // Fetch jobs from API
        async function fetchJobs() {
            try {
                // In a real app, you would fetch from your actual API endpoint
                // const response = await fetch('/api/jobs');
                // const data = await response.json();
                
                // For this demo, we'll use the mock data you provided
                const mockData = [
                  {
                    "id": 4,
                    "title": "webdev",
                    "description": "Designs, develops, and maintains websites and web applications. Uses programming languages like HTML, CSS, JavaScript, and frameworks such as React or Vue.js. Ensures site functionality, responsiveness, and security.",
                    "requirements": "Bachelor's degree in Computer Science, IT, or a related field (optional for self-taught developers with a strong portfolio)\nProficiency in HTML, CSS, JavaScript\nKnowledge of frameworks like React, Vue.js, or Angular\nExperience with version control (Git, GitHub)\nProblem-solving and debugging skills",
                    "salary": "5000",
                    "location": "pasig",
                    "schedule": "Full-Time",
                    "department": "IT Department",
                    "status": "published",
                    "created_at": "2025-03-07T15:42:30.000000Z",
                    "updated_at": "2025-03-07T15:42:45.000000Z"
                  },
                  {
                    "id": 5,
                    "title": "Software Engineer",
                    "description": "Develops, tests, and maintains software applications. Works with programming languages like Java, Python, or C++ to create scalable and efficient software solutions.",
                    "requirements": "Bachelor's degree in Computer Science, Software Engineering, or related field\nStrong programming skills in Java, Python, or C++\nKnowledge of software development methodologies (Agile, Scrum)\nExperience with databases (SQL, NoSQL)\nAnalytical and problem-solving skills",
                    "salary": "30000",
                    "location": "pasig",
                    "schedule": "WFH",
                    "department": "IT Department",
                    "status": "published",
                    "created_at": "2025-03-07T15:44:43.000000Z",
                    "updated_at": "2025-03-07T15:47:52.000000Z"
                  },
                  {
                    "id": 6,
                    "title": "IT Support Specialist",
                    "description": "Provides technical support for hardware, software, and network issues. Troubleshoots and resolves IT-related problems for businesses and users.",
                    "requirements": "Bachelor's degree in IT, Computer Science, or related field (or equivalent experience)\nKnowledge of computer hardware, operating systems, and networking\nTroubleshooting and problem-solving skills\nGood communication skills for assisting non-technical users\nCertifications like CompTIA A+ (optional but beneficial)",
                    "salary": "10000",
                    "location": "Quezon City",
                    "schedule": "Part-Time",
                    "department": "IT Department",
                    "status": "published",
                    "created_at": "2025-03-07T15:45:25.000000Z",
                    "updated_at": "2025-03-07T15:47:49.000000Z"
                  },
                  {
                    "id": 7,
                    "title": "Graphic Designer",
                    "description": "Creates visual designs for marketing materials, branding, and digital content using design software like Adobe Photoshop, Illustrator, or Figma.",
                    "requirements": "Bachelor's degree in Graphic Design, Fine Arts, or related field (or strong portfolio)\nProficiency in Adobe Photoshop, Illustrator, and/or Figma\nCreativity and attention to detail\nAbility to work with deadlines and collaborate with teams\nExperience in UI/UX design (optional but beneficial)",
                    "salary": "30000",
                    "location": "Quezon City",
                    "schedule": "Full-Time",
                    "department": "IT Department",
                    "status": "pending",
                    "created_at": "2025-03-07T15:47:43.000000Z",
                    "updated_at": "2025-03-07T15:47:43.000000Z"
                  },
                  {
                    "id": 8,
                    "title": "Sales Merchandiser",
                    "description": "Ensures products are properly displayed in retail stores to attract customers. Works with store staff to arrange promotions, refill shelves, and check inventory.",
                    "requirements": "High school diploma or vocational certificate\nExperience in retail or sales is a plus but not required\nGood communication and customer service skills\nBasic inventory management knowledge",
                    "salary": "50000",
                    "location": "Quezon City",
                    "schedule": "Full-Time",
                    "department": "Sales Department",
                    "status": "private",
                    "created_at": "2025-03-07T17:19:04.000000Z",
                    "updated_at": "2025-03-07T21:25:42.000000Z"
                  },
                  {
                    "id": 9,
                    "title": "Promodiser",
                    "description": "Promotes specific brands or products in stores, convinces customers to buy, and sometimes offers free samples or demos. Helps track sales and customer feedback.",
                    "requirements": "High school diploma or vocational ,certificate\nStrong sales and persuasion skills,\nFriendly and engaging personality,\nAbility to stand for long hours",
                    "salary": "10000",
                    "location": "Quezon City",
                    "schedule": "Full-Time",
                    "department": "Sales Department",
                    "status": "published",
                    "created_at": "2025-03-07T17:21:28.000000Z",
                    "updated_at": "2025-03-07T17:21:34.000000Z"
                  },
                  {
                    "id": 10,
                    "title": "Field Sales Merchandiser",
                    "description": "Visits different retail stores or outlets to ensure products are well-stocked, properly displayed, and priced correctly. Builds relationships with store managers to improve product visibility.",
                    "requirements": "High school diploma or vocational training\nExperience in sales or merchandising is a plus\nWillingness to travel to different store locations\nNegotiation and communication skills",
                    "salary": "10000",
                    "location": "Quezon City",
                    "schedule": "Full-Time",
                    "department": "Sales Department",
                    "status": "published",
                    "created_at": "2025-03-07T20:44:32.000000Z",
                    "updated_at": "2025-03-07T20:45:25.000000Z"
                  },
                  {
                    "id": 12,
                    "title": "web dev",
                    "description": "test",
                    "requirements": "test",
                    "salary": "30000",
                    "location": "QC",
                    "schedule": "WFH",
                    "department": "IT Department",
                    "status": "published",
                    "created_at": "2025-03-08T02:46:40.000000Z",
                    "updated_at": "2025-03-08T06:47:36.000000Z"
                  },
                  {
                    "id": 13,
                    "title": "TEST",
                    "description": "asd,mna,smdnm,asnd",
                    "requirements": "ajsdbajkshdkjhasd",
                    "salary": "19999",
                    "location": "CALOOCAN",
                    "schedule": "Full-Time",
                    "department": "IT Department",
                    "status": "pending",
                    "created_at": "2025-03-08T06:37:08.000000Z",
                    "updated_at": "2025-03-08T06:37:08.000000Z"
                  }
                ];
                
                allJobs = mockData;
                renderJobs(allJobs);
                loadingIndicator.classList.add('hidden');
                
            } catch (error) {
                console.error('Error fetching jobs:', error);
                loadingIndicator.innerHTML = '<p class="text-red-500">Error loading jobs. Please try again.</p>';
            }
        }
        
        // Render jobs to the DOM
        function renderJobs(jobs) {
            if (jobs.length === 0) {
                emptyState.classList.remove('hidden');
                jobContainer.innerHTML = '';
                return;
            }
            
            emptyState.classList.add('hidden');
            
            jobContainer.innerHTML = jobs.map(job => `
                <div class="job-card flex flex-col bg-white rounded-lg overflow-hidden cursor-pointer border border-gray-200 h-full" 
                    onclick="openModal(${job.id})">
                    
                    <div class="p-5 pb-3">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-800">${job.title}</h3>
                            <span class="text-xs px-2 py-1 rounded-full status-${job.status}">
                                ${job.status.charAt(0).toUpperCase() + job.status.slice(1)}
                            </span>
                        </div>
                        <p class="text-gray-600 mb-3 flex items-center text-sm">
                            <i class="fas fa-building mr-2"></i>${job.department}
                        </p>
                        <p class="text-gray-600 mb-4 flex items-center text-sm">
                            <i class="fas fa-map-marker-alt mr-2"></i>${job.location}
                        </p>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Salary:</span>
                                <span class="font-medium text-gray-700">₱${parseInt(job.salary).toLocaleString()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Schedule:</span>
                                <span class="font-medium text-gray-700">${job.schedule}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-auto px-5 py-3 bg-gray-50 border-t border-gray-100">
                        <div class="flex justify-between text-xs text-gray-500 mb-2">
                            <span>Posted: ${formatDate(job.created_at)}</span>
                            <span>Updated: ${formatDate(job.updated_at)}</span>
                        </div>
                        <button class="w-full linear-gradient  hover:bg-blue-700 text-black py-1.5 px-4 rounded-md text-sm font-medium transition">
                            View Details
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        // Filter jobs based on current filters
        function filterJobs() {
            filteredJobs = allJobs.filter(job => {
                // Status filter
                const statusMatch = currentStatusFilter === 'all' || job.status === currentStatusFilter;
                
                // Department filter
                const deptMatch = currentDepartmentFilter === 'all' || job.department === currentDepartmentFilter;
                
                // Schedule filter
                const scheduleMatch = currentScheduleFilter === 'all' || job.schedule === currentScheduleFilter;
                
                // Search term
                const searchMatch = currentSearchTerm === '' || 
                    job.title.toLowerCase().includes(currentSearchTerm) || 
                    job.description.toLowerCase().includes(currentSearchTerm) ||
                    job.department.toLowerCase().includes(currentSearchTerm);
                
                return statusMatch && deptMatch && scheduleMatch && searchMatch;
            });
            
            renderJobs(filteredJobs);
        }
        
        // Format date for display
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        
        // Open job detail modal
        function openModal(jobId) {
            const job = allJobs.find(j => j.id === jobId);
            if (!job) return;
            
            modalTitle.textContent = job.title;
            modalDept.textContent = job.department;
            modalLocation.textContent = job.location;
            modalSalary.textContent = `₱${parseInt(job.salary).toLocaleString()}`;
            modalSchedule.textContent = job.schedule;
            modalStatus.textContent = job.status.charAt(0).toUpperCase() + job.status.slice(1);
            modalDescription.textContent = job.description;
            modalRequirements.textContent = job.requirements;
            modalCreatedAt.textContent = `Posted: ${formatDate(job.created_at)}`;
            modalUpdatedAt.textContent = `Updated: ${formatDate(job.updated_at)}`;
            
            // Set status badge class
            modalStatus.className = 'font-medium';
            modalStatus.classList.add(`status-${job.status}`);
            
            jobModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        // Close modal
        function closeModal() {
            jobModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Event Listeners
        searchInput.addEventListener('input', (e) => {
            currentSearchTerm = e.target.value.toLowerCase();
            filterJobs();
        });
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('tab-active'));
                button.classList.add('tab-active');
                currentStatusFilter = button.dataset.status;
                filterJobs();
            });
        });
        
        filterChips.forEach(chip => {
            chip.addEventListener('click', () => {
                const filterValue = chip.dataset.filter;
                
                if (filterValue === 'all') {
                    currentDepartmentFilter = 'all';
                    currentScheduleFilter = 'all';
                } else if (['IT Department', 'Sales Department'].includes(filterValue)) {
                    currentDepartmentFilter = filterValue;
                } else {
                    currentScheduleFilter = filterValue;
                }
                
                filterJobs();
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === jobModal) {
                closeModal();
            }
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', fetchJobs);
    </script>
</body>
</html>