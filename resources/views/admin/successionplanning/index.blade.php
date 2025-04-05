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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .progress-ring__circle {
            transition: stroke-dashoffset 0.5s ease;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="main-content min-h-screen font-sans bg-gray-50" x-data="successionPlanning()" x-cloak>
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <main class="flex-grow p-4 md:p-8">
            <div class="container mx-auto">
                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Succession Planning</h1>
                    <p class="text-gray-600 mt-2">Review employee readiness for advancement and development opportunities</p>
                </div>

                <!-- Filters and Actions -->
                <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                        <div class="relative w-full sm:w-auto">
                            <select x-model="filters.department" class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm md:text-base">
                                <option value="All Departments">All Departments</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Marketing">Marketing</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Operations">Operations</option>
                                <option value="Product">Product</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        <div class="relative w-full sm:w-auto">
                            <select x-model="filters.status" class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm md:text-base">
                                <option value="All Statuses">All Statuses</option>
                                <option value="Ready Now">Ready Now</option>
                                <option value="1-2 Years">1-2 Years</option>
                                <option value="Development Needed">Development Needed</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-auto">
                    </div>
                </div>

                <!-- Employee Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="employee in filteredEmployees" :key="employee.id">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <!-- Employee Header -->
                            <div class="bg-gradient-to-r from-blue-50 to-gray-50 p-6 border-b border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div class="bg-blue-100 text-blue-800 rounded-full w-12 h-12 flex items-center justify-center font-semibold text-lg">
                                        <span x-text="employee.initials"></span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-lg text-gray-800" x-text="employee.name"></h3>
                                        <p class="text-gray-500 text-sm">Employee ID: <span x-text="employee.id"></span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Employee Details -->
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Current Position</p>
                                        <p class="font-medium" x-text="employee.position"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Department</p>
                                        <p class="font-medium" x-text="employee.department"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Experience</p>
                                        <p class="font-medium" x-text="employee.experience"></p>
                                    </div>
                                    
                                    <!-- Score and Status -->
                                    <div class="flex justify-between items-center pt-3">
    <div>
        <p class="text-sm text-gray-500">Readiness Score</p>
        <div class="flex items-center gap-2">
            <div class="relative w-16 h-16 flex items-center justify-center">
                <svg class="w-full h-full" viewBox="0 0 36 36">
                    <path
                        d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none"
                        stroke="#E5E7EB"
                        stroke-width="3"
                        stroke-dasharray="100, 100"
                    />
                    <path
                        class="progress-ring__circle"
                        d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none"
                        :stroke="employee.status === 'Ready Now' ? '#10B981' : (employee.status === '1-2 Years' ? '#F59E0B' : '#EF4444')"
                        stroke-width="3"
                        :stroke-dasharray="employee.score + ', 100'"
                    />
                </svg>
                <span class="absolute text-sm font-medium" x-text="employee.score + '%'"></span>
            </div>
        </div>
    </div>
    <span class="px-3 py-1 rounded-full text-xs font-semibold" 
          :class="{
              'bg-green-100 text-green-800': employee.status === 'Ready Now',
              'bg-yellow-100 text-yellow-800': employee.status === '1-2 Years',
              'bg-red-100 text-red-800': employee.status === 'Development Needed'
          }">
        <span x-text="employee.status"></span>
    </span>
</div>
                                    
                                    <!-- Recommendation -->
                                    <div class="mt-4 p-4 rounded-lg border" 
                                         :class="{
                                             'bg-green-100 border-green-100': employee.status === 'Ready Now',
                                             'bg-yellow-100 border-yellow-100': employee.status === '1-2 Years',
                                             'bg-red-100 border-red-100': employee.status === 'Development Needed'
                                         }">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-0.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" 
                                                     :class="{
                                                         'text-green-600': employee.status === 'Ready Now',
                                                         'text-yellow-600': employee.status === '1-2 Years',
                                                         'text-red-600': employee.status === 'Development Needed'
                                                     }" 
                                                     viewBox="0 0 20 20" fill="currentColor">
                                                    <path x-show="employee.status === 'Ready Now'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    <path x-show="employee.status === '1-2 Years'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 1 1 0 001.415 1.414z" clip-rule="evenodd" />
                                                    <path x-show="employee.status === 'Development Needed'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium" 
                                                   :class="{
                                                       'text-green-800': employee.status === 'Ready Now',
                                                       'text-yellow-800': employee.status === '1-2 Years',
                                                       'text-red-800': employee.status === 'Development Needed'
                                                   }">
                                                    <span x-show="employee.status === 'Ready Now'">Ready for promotion</span>
                                                    <span x-show="employee.status === '1-2 Years'">Potential in 1-2 years</span>
                                                    <span x-show="employee.status === 'Development Needed'">Needs development</span>
                                                </p>
                                                <p class="text-sm mt-1" 
                                                   :class="{
                                                       'text-green-800': employee.status === 'Ready Now',
                                                       'text-yellow-800': employee.status === '1-2 Years',
                                                       'text-red-800': employee.status === 'Development Needed'
                                                   }">
                                                    <span x-show="employee.status === 'Ready Now'">Potential role: <span class="font-medium" x-text="employee.department + ' Lead'"></span></span>
                                                    <span x-show="employee.status === '1-2 Years'">Development focus: <span class="font-medium">Strategic Leadership</span></span>
                                                    <span x-show="employee.status === 'Development Needed'">Recommended: <span class="font-medium">Skills Training</span></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3">
                                    <button 
                                        @click="openModal = true; selectedEmployee = employee"
                                        class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    <nav class="inline-flex rounded-md shadow">
                        <a href="#" class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="px-4 py-2 border-t border-b border-gray-300 bg-white text-blue-600 font-medium">1</a>
                        <a href="#" class="px-4 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 font-medium">2</a>
                        <a href="#" class="px-4 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 font-medium">3</a>
                        <a href="#" class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </nav>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
<div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 bg-opacity-50" @click="openModal = false"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 flex justify-between items-center border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Employee Succession Details</h3>
                <button @click="openModal = false" class="text-gray-700 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 space-y-6 overflow-y-auto" style="max-height: calc(90vh - 120px)">
                <!-- Profile Section -->
                <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                    <div class="flex-shrink-0">
                        <div class="bg-blue-100 text-blue-800 rounded-full w-32 h-32 flex items-center justify-center font-semibold text-4xl">
                            <span x-text="selectedEmployee ? selectedEmployee.initials : ''"></span>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <h2 class="text-2xl font-bold text-gray-800" x-text="selectedEmployee ? selectedEmployee.name : ''"></h2>
                        <p class="text-lg text-gray-600" x-text="selectedEmployee ? selectedEmployee.position : ''"></p>
                        <p class="text-gray-500" x-text="selectedEmployee ? selectedEmployee.department : ''"></p>
                        
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p><span class="info-label">Employee ID:</span> <span class="info-value" x-text="selectedEmployee ? selectedEmployee.id : ''"></span></p>
                                <p><span class="info-label">Status:</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          :class="{
                                              'bg-green-100 text-green-800': selectedEmployee && selectedEmployee.status === 'Ready Now',
                                              'bg-yellow-100 text-yellow-800': selectedEmployee && selectedEmployee.status === '1-2 Years',
                                              'bg-red-100 text-red-800': selectedEmployee && selectedEmployee.status === 'Development Needed'
                                          }">
                                        <span x-text="selectedEmployee ? selectedEmployee.status : ''"></span>
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p><span class="info-label">Experience:</span> <span class="info-value" x-text="selectedEmployee ? selectedEmployee.experience : ''"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="section-divider my-6">
                
                <!-- Readiness Score -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Readiness Assessment</h3>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex items-center gap-4">
                            <div class="relative w-32 h-32 flex items-center justify-center">
                                <svg class="w-full h-full" viewBox="0 0 36 36">
                                    <path
                                        d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none"
                                        stroke="#E5E7EB"
                                        stroke-width="3"
                                        stroke-dasharray="100, 100"
                                    />
                                    <path
                                        class="progress-ring__circle"
                                        d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none"
                                        :stroke="selectedEmployee && selectedEmployee.status === 'Ready Now' ? '#10B981' : (selectedEmployee && selectedEmployee.status === '1-2 Years' ? '#F59E0B' : '#EF4444')"
                                        stroke-width="3"
                                        :stroke-dasharray="selectedEmployee ? selectedEmployee.score + ', 100' : '0, 100'"
                                    />
                                </svg>
                                <span class="absolute text-2xl font-bold" x-text="selectedEmployee ? selectedEmployee.score + '%' : '0%'"></span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600" x-text="selectedEmployee && selectedEmployee.status === 'Ready Now' ? 'This employee is ready for promotion now.' : (selectedEmployee && selectedEmployee.status === '1-2 Years' ? 'This employee has potential for promotion in 1-2 years.' : 'This employee needs development before promotion.')"></p>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="p-4 rounded-lg border" 
                                 :class="{
                                     'bg-green-100 border-green-100': selectedEmployee && selectedEmployee.status === 'Ready Now',
                                     'bg-yellow-100 border-yellow-100': selectedEmployee && selectedEmployee.status === '1-2 Years',
                                     'bg-red-100 border-red-100': selectedEmployee && selectedEmployee.status === 'Development Needed'
                                 }">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" 
                                             :class="{
                                                 'text-green-600': selectedEmployee && selectedEmployee.status === 'Ready Now',
                                                 'text-yellow-600': selectedEmployee && selectedEmployee.status === '1-2 Years',
                                                 'text-red-600': selectedEmployee && selectedEmployee.status === 'Development Needed'
                                             }" 
                                             viewBox="0 0 20 20" fill="currentColor">
                                            <path x-show="selectedEmployee && selectedEmployee.status === 'Ready Now'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            <path x-show="selectedEmployee && selectedEmployee.status === '1-2 Years'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 1 1 0 001.415 1.414z" clip-rule="evenodd" />
                                            <path x-show="selectedEmployee && selectedEmployee.status === 'Development Needed'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium" 
                                           :class="{
                                               'text-green-800': selectedEmployee && selectedEmployee.status === 'Ready Now',
                                               'text-yellow-800': selectedEmployee && selectedEmployee.status === '1-2 Years',
                                               'text-red-800': selectedEmployee && selectedEmployee.status === 'Development Needed'
                                           }">
                                            <span x-show="selectedEmployee && selectedEmployee.status === 'Ready Now'">Ready for promotion</span>
                                            <span x-show="selectedEmployee && selectedEmployee.status === '1-2 Years'">Potential in 1-2 years</span>
                                            <span x-show="selectedEmployee && selectedEmployee.status === 'Development Needed'">Needs development</span>
                                        </p>
                                        <p class="text-sm mt-1" 
                                           :class="{
                                               'text-green-800': selectedEmployee && selectedEmployee.status === 'Ready Now',
                                               'text-yellow-800': selectedEmployee && selectedEmployee.status === '1-2 Years',
                                               'text-red-800': selectedEmployee && selectedEmployee.status === 'Development Needed'
                                           }">
                                            <span x-show="selectedEmployee && selectedEmployee.status === 'Ready Now'">Potential role: <span class="font-medium" x-text="selectedEmployee ? selectedEmployee.department + ' Lead' : ''"></span></span>
                                            <span x-show="selectedEmployee && selectedEmployee.status === '1-2 Years'">Development focus: <span class="font-medium">Strategic Leadership</span></span>
                                            <span x-show="selectedEmployee && selectedEmployee.status === 'Development Needed'">Recommended: <span class="font-medium">Skills Training</span></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Work Description -->
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Role Description</p>
                    <p class="mt-2 text-gray-700" x-text="selectedEmployee ? selectedEmployee.description : ''"></p>
                </div>
                
                <!-- Key Strengths -->
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Key Strengths</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <template x-for="strength in (selectedEmployee ? selectedEmployee.strengths : [])">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="strength"></span>
                        </template>
                    </div>
                </div>
                
                <!-- Recent Achievements -->
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Recent Achievements</p>
                    <ul class="mt-2 space-y-2 text-gray-700 pl-5 list-disc">
                        <template x-for="achievement in (selectedEmployee ? selectedEmployee.achievements : [])">
                            <li x-text="achievement"></li>
                        </template>
                    </ul>
                </div>
                
                <!-- Development Plan -->
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Development Plan</p>
                    <div class="mt-4 space-y-4">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800">Recommended Training</p>
                                <p class="text-sm text-gray-600" x-text="selectedEmployee && selectedEmployee.status === 'Ready Now' ? 'Advanced Leadership Program' : (selectedEmployee && selectedEmployee.status === '1-2 Years' ? 'Strategic Management Course' : 'Core Skills Development')"></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800">Potential Future Role</p>
                                <p class="text-sm text-gray-600" x-text="selectedEmployee ? selectedEmployee.department + ' Lead' : ''"></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800">Timeline</p>
                                <p class="text-sm text-gray-600" x-text="selectedEmployee && selectedEmployee.status === 'Ready Now' ? 'Immediate promotion consideration' : (selectedEmployee && selectedEmployee.status === '1-2 Years' ? '12-24 month development plan' : '6-12 month intensive training')"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-200">
                <div>
                    <p class="text-sm text-gray-500">Last updated: {{ date('M d, Y') }}</p>
                </div>
                <div class="flex space-x-3">
                    <button @click="openModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">
                        Close
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z" />
                        </svg>
                        Assign Mentor
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        function successionPlanning() {
            return {
                openModal: false,
                selectedEmployee: null,
                filters: {
                    department: 'All Departments',
                    status: 'All Statuses'
                },
                employees: [
                    @foreach ($successionplanning as $index => $succession)
                        @php
                            // Varied data for demonstration
                            $positions = ['Senior Developer', 'Marketing Manager', 'HR Business Partner', 'Financial Analyst', 'Operations Lead', 'Product Owner'];
                            $departments = ['Engineering', 'Marketing', 'HR', 'Finance', 'Operations', 'Product'];
                            $experiences = ['3 years', '5 years', '7 years', '2 years', '10 years', '4 years'];
                            $statuses = ['Ready Now', '1-2 Years', 'Development Needed'];
                            $currentStatus = $statuses[$index % count($statuses)];
                            $currentPosition = $positions[$index % count($positions)];
                            $currentDepartment = $departments[$index % count($departments)];
                            $currentExperience = $experiences[$index % count($experiences)];
                            
                            // Unique work descriptions based on position and department
                            $workDescriptions = [
                                'Senior Developer' => "Leads development of critical software components, mentors junior developers, and implements best practices in code quality and system architecture.",
                                'Marketing Manager' => "Oversees all marketing campaigns, analyzes market trends, and develops strategies to increase brand awareness and customer engagement.",
                                'HR Business Partner' => "Acts as a consultant to management on human resource-related issues and aligns HR strategies with business objectives.",
                                'Financial Analyst' => "Prepares financial reports, forecasts, and analyses to support business decisions and ensure financial health of the organization.",
                                'Operations Lead' => "Optimizes operational processes, manages cross-functional teams, and implements efficiency improvements across departments.",
                                'Product Owner' => "Defines product vision, manages product backlog, and works closely with stakeholders to deliver valuable product features."
                            ];
                            $currentWorkDescription = $workDescriptions[$currentPosition] ?? "Responsible for key departmental functions and contributing to organizational success.";
                            
                            // Strengths based on department
                            $strengths = [
                                'Engineering' => ["Technical expertise", "Problem-solving", "Attention to detail", "Innovation"],
                                'Marketing' => ["Creativity", "Communication", "Data analysis", "Strategic thinking"],
                                'HR' => ["People skills", "Conflict resolution", "Organizational knowledge", "Coaching"],
                                'Finance' => ["Analytical skills", "Risk assessment", "Financial modeling", "Regulatory knowledge"],
                                'Operations' => ["Process optimization", "Team leadership", "Supply chain management", "Efficiency focus"],
                                'Product' => ["User empathy", "Market awareness", "Prioritization", "Stakeholder management"]
                            ];
                            $currentStrengths = $strengths[$currentDepartment] ?? ["Adaptability", "Collaboration", "Results-driven"];
                            
                            // Achievements
                            $achievement1 = $currentDepartment === 'Engineering' ? 'Led the development of a key feature that increased user engagement by 25%' : 
                                          ($currentDepartment === 'Marketing' ? 'Designed campaign that generated 500K in new revenue' : 
                                          ($currentDepartment === 'HR' ? 'Implemented employee wellness program with 90% participation' : 
                                          ($currentDepartment === 'Finance' ? 'Identified cost savings of 200K through process optimization' : 
                                          ($currentDepartment === 'Operations' ? 'Reduced operational costs by 15% through process improvements' : 
                                          'Launched new product feature with 95% customer satisfaction'))));
                            
                            $achievement2 = $currentDepartment === 'Engineering' ? 'Mentored 3 junior developers to senior level' : 
                                          ($currentDepartment === 'Marketing' ? 'Increased social media following by 40% in 6 months' : 
                                          ($currentDepartment === 'HR' ? 'Reduced employee turnover by 20% through retention initiatives' : 
                                          ($currentDepartment === 'Finance' ? 'Streamlined reporting process saving 10 hours per week' : 
                                          ($currentDepartment === 'Operations' ? 'Improved supply chain efficiency reducing delays by 30%' : 
                                          'Prioritized and delivered 90% of roadmap items on time'))));
                        @endphp
                        {
                            id: '{{ $succession['id'] }}',
                            name: '{{ $succession['first_name'] }} {{ $succession['last_name'] }}',
                            initials: '{{ substr($succession['first_name'], 0, 1) }}{{ substr($succession['last_name'], 0, 1) }}',
                            position: '{{ $currentPosition }}',
                            department: '{{ $currentDepartment }}',
                            experience: '{{ $currentExperience }}',
                            score: '{{ $succession['final_score'] }}',
                            status: '{{ $currentStatus }}',
                            description: '{{ $currentWorkDescription }}',
                            strengths: @json($currentStrengths),
                            achievements: [
                                '{{ $achievement1 }}',
                                '{{ $achievement2 }}'
                            ]
                        },
                    @endforeach
                ],
                get filteredEmployees() {
                    return this.employees.filter(employee => {
                        const departmentMatch = this.filters.department === 'All Departments' || 
                                              employee.department === this.filters.department;
                        const statusMatch = this.filters.status === 'All Statuses' || 
                                          employee.status === this.filters.status;
                        return departmentMatch && statusMatch;
                    });
                }
            }
        }
    </script>
</body>
</html>