<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS Employee Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @use('Carbon\Carbon')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="main-content bg-gray-50">
    @include('layouts.navigation')

    <div class="flex min-h-screen">
        @include('layouts.sidebar')

        <div class="flex-1 p-6">
            <div class="flex items-stretch gap-4 mb-6">
                <!-- Welcome Banner (60% width) -->
                <div class="bg-gradient-to-r from-yellow-100 via-yellow-200 to-orange-300 p-6 rounded-xl shadow-lg transform transition-all duration-500 hover:shadow-xl animate-fade-in-up w-3/5">
                    <div class="flex items-center h-full">
                        <div class="mr-6">
                            @if(auth()->user()->profilePic)
                            <img src="{{ Storage::url(auth()->user()->profilePic) }}" 
                                 alt="Profile Picture" 
                                 class="w-24 h-24 rounded-full object-cover border-4 border-white/30 shadow-md hover:scale-105 transition-transform duration-300">
                            @else
                            <div class="w-24 h-24 rounded-full bg-yellow-200 flex items-center justify-center border-4 border-white/30 shadow-md">
                                <span class="text-4xl font-bold text-orange-600">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-3xl font-bold mb-2 text-gray-800">{{ auth()->user()->name }}</h2>
                            <p class="text-orange-700 text-lg font-medium">
                                @if($employeeRecord)
                                {{ $employeeRecord->position->name }} in {{ $employeeRecord->department->name }}
                                @else
                                Employee
                                @endif
                            </p>
                            <div class="mt-4">
                                <div class="bg-white/40 px-4 py-2 rounded-lg text-orange-800 text-base inline-block shadow-sm hover:shadow-md transition-shadow">
                                    <i class="fas fa-id-card mr-2"></i>
                                    <span class="font-semibold">ID: {{ auth()->user()->user_id }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar (40% width) - Fixed Philippine Calendar -->
                <div class="bg-white p-4 rounded-lg shadow-lg w-2/5">
                    <div class="text-center mb-3">
                        <h3 class="font-bold text-gray-800">{{ now()->timezone('Asia/Manila')->format('F Y') }}</h3>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-xs text-center">
                        <!-- Day headers (Sunday-first week) -->
                        <div class="font-bold text-gray-500">Sun</div>
                        <div class="font-bold text-gray-500">Mon</div>
                        <div class="font-bold text-gray-500">Tue</div>
                        <div class="font-bold text-gray-500">Wed</div>
                        <div class="font-bold text-gray-500">Thu</div>
                        <div class="font-bold text-gray-500">Fri</div>
                        <div class="font-bold text-gray-500">Sat</div>
                        
                        <!-- Calendar days -->
                        @php
                            // Philippine timezone with Sunday as first day of week
                            $now = now()->timezone('Asia/Manila');
                            $firstDay = $now->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
                            $lastDay = $now->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
                            $currentDay = $firstDay->copy();
                            $today = $now->format('Y-m-d');
                        @endphp
                        
                        @while($currentDay <= $lastDay)
                            <div class="p-1 rounded-full 
                                @if($currentDay->format('Y-m-d') == $today) bg-orange-300 text-white font-bold
                                @elseif($currentDay->format('m') != $now->format('m')) text-gray-300
                                @endif">
                                {{ $currentDay->format('d') }}
                            </div>
                            @php $currentDay->addDay(); @endphp
                        @endwhile
                    </div>
                    <div class="mt-3 text-center text-xs text-gray-600">
                        <i class="fas fa-circle text-orange-300 mr-1"></i> Today
                    </div>
                </div>
            </div>

            <!-- Action Required Section -->
            @if(!$employeeRecord || count($pendingDocuments) > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400 text-xl mt-1 mr-3"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-yellow-800">Action Required</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @if(!$employeeRecord)
                                <li>Please complete your employee record <a href="{{ route('employee.records.create') }}" class="text-blue-600 hover:underline">here</a></li>
                                @endif
                                @if(count($pendingDocuments) > 0)
                                <li>You need to upload {{ count($pendingDocuments) }} documents</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Record Status Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Employee Record Status -->
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 {{ $employeeRecord ? 'border-green-500' : 'border-red-500' }}">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Employee Record</h3>
                        <i class="fas fa-user-tie {{ $employeeRecord ? 'text-green-500' : 'text-red-500' }}"></i>
                    </div>
                    
                    @if($employeeRecord)
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium">Complete</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="text-sm">{{ $employeeRecord->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Employment:</span>
                            <span class="capitalize">{{ $employeeRecord->employment_status ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <button onclick="window.location.href='{{ route('employee.records.index') }}'" class="mt-4 w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition duration-200">
                        <i class="fas fa-eye mr-2"></i> View Record
                    </button>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-3"></i>
                        <p class="text-gray-700 mb-4">You haven't created your employee record yet.</p>
                        <button onclick="window.location.href='{{ route('employee.records.create') }}'" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg transition duration-200">
                            <i class="fas fa-plus mr-2"></i> Create Record
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Document Status -->
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Document Status</h3>
                        <i class="fas fa-file-alt text-blue-500"></i>
                    </div>
                    
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium">Completion</span>
                            <span class="text-sm font-bold">{{ $documentCompletionPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $documentCompletionPercentage }}%"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Uploaded Documents:</span>
                            <span class="font-medium">{{ count($uploadedDocuments) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Pending Documents:</span>
                            <span class="font-medium">{{ count($pendingDocuments) }}</span>
                        </div>
                    </div>
                    
                    <button onclick="window.location.href='{{ route('employee.documents.index') }}'" class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition duration-200">
                        <i class="fas fa-folder-open mr-2"></i> View Documents
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-purple-500">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
                        <i class="fas fa-bolt text-purple-500"></i>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="window.location.href='{{ route('employee.documents.upload') }}'" class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-3 rounded-lg flex flex-col items-center transition duration-200">
                            <i class="fas fa-file-upload text-xl mb-1"></i>
                            <span class="text-sm">Upload Document</span>
                        </button>
                        <button onclick="window.location.href='{{ route('employee.records.' . ($employeeRecord ? 'edit' : 'create'), $employeeRecord ? $employeeRecord->id : '') }}'" class="bg-green-100 hover:bg-green-200 text-green-800 p-3 rounded-lg flex flex-col items-center transition duration-200">
                            <i class="fas fa-user-edit text-xl mb-1"></i>
                            <span class="text-sm">{{ $employeeRecord ? 'Update' : 'Create' }} Record</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white p-6 rounded-xl shadow-md mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">My Documents</h3>
                    <a href="{{ route('employee.documents.index') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">View All</a>
                </div>
                
                @if(count($pendingDocuments) > 0)
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-red-800">Pending Documents Required</h4>
                            <ul class="list-disc pl-5 mt-1 text-sm text-red-700">
                                @foreach($pendingDocuments as $docType)
                                <li>{{ $documentTypeNames[$docType] ?? $docType }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button onclick="window.location.href='{{ route('employee.documents.upload') }}'" class="mt-3 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Now
                    </button>
                </div>
                @endif
                
                @if(count($uploadedDocuments) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($uploadedDocuments as $document)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                        <span>{{ $documentTypeNames[$document->document_type] ?? $document->document_type }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $document->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($document->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($document->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $document->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('protected.files', ['user_id' => auth()->user()->user_id, 'filename' => basename($document->file_path)]) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-900 mr-3"
                                       title="View Document">
                                       <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <a href="{{ route('protected.files.download', ['user_id' => auth()->user()->user_id, 'filename' => basename($document->file_path)]) }}"
                                       class="text-green-600 hover:text-green-900"
                                       title="Download Document">
                                       <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-file-import text-4xl mb-3"></i>
                    <p>No documents uploaded yet</p>
                    <button onclick="window.location.href='{{ route('employee.documents.upload') }}'" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Your First Document
                    </button>
                </div>
                @endif
            </div>

            <!-- Recent Activity Section -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Recent Activity</h3>
                </div>
                
                <div class="space-y-4">
                    @if($employeeRecord)
                    <div class="flex items-start">
                        <div class="bg-purple-100 p-3 rounded-full mr-4">
                            <i class="fas fa-user-edit text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium">Record Updated</h4>
                            <p class="text-sm text-gray-600">You updated your employee record</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $employeeRecord->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if(count($uploadedDocuments) > 0)
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-file-upload text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium">Document Uploaded</h4>
                            <p class="text-sm text-gray-600">{{ $documentTypeNames[$uploadedDocuments[0]->document_type] ?? $uploadedDocuments[0]->document_type }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $uploadedDocuments[0]->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-start">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium">Login Activity</h4>
                            <p class="text-sm text-gray-600">You logged in to the system</p>
                            <p class="text-xs text-gray-400 mt-1">{{ now()->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show notification if no record exists
        document.addEventListener('DOMContentLoaded', function() {
            @if(!$employeeRecord)
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center z-50';
            toast.innerHTML = `
                <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                <div>
                    <p class="font-medium">Employee record required!</p>
                    <p class="text-sm">Please complete your record to access all features.</p>
                </div>
                <button onclick="window.location.href='{{ route('employee.records.create') }}'" class="ml-6 bg-white text-red-500 px-3 py-1 rounded text-sm font-medium">
                    Create Now
                </button>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 10000);
            @endif
            
            @if(count($pendingDocuments) > 0 && $employeeRecord)
            const docToast = document.createElement('div');
            docToast.className = 'fixed bottom-4 right-4 bg-yellow-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center z-40';
            docToast.innerHTML = `
                <i class="fas fa-file-exclamation mr-3 text-xl"></i>
                <div>
                    <p class="font-medium">Documents pending upload!</p>
                    <p class="text-sm">You have {{ count($pendingDocuments) }} required documents to upload.</p>
                </div>
                <button onclick="window.location.href='{{ route('employee.documents.upload') }}'" class="ml-6 bg-white text-yellow-500 px-3 py-1 rounded text-sm font-medium">
                    Upload Now
                </button>
            `;
            document.body.appendChild(docToast);
            
            setTimeout(() => {
                docToast.remove();
            }, 10000);
            @endif

            // Auto-refresh at midnight Philippine time
            function refreshAtMidnight() {
                const now = new Date();
                const phTime = new Date(now.getTime() + (8 * 60 * 60 * 1000)); // Convert to PH time
                const midnight = new Date(phTime);
                midnight.setHours(24, 0, 0, 0);
                
                const timeout = midnight.getTime() - (now.getTime() + (8 * 60 * 60 * 1000));
                
                setTimeout(() => {
                    window.location.reload();
                }, timeout);
            }

            refreshAtMidnight();
        });
    </script>
</body>
</html>