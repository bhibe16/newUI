<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - My Documents</title>
    @production
    <link rel="stylesheet" href="{{ asset('build/assets/style-Wg8zdAtV.css') }}">
    <script type="module" src="{{ asset('build/assets/app-LM_T2gVS.js') }}"></script>
    @else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endproduction
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="main-content min-h-screen bg-gray-50">

    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 sm:p-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">My Documents</h1>
                        <p class="text-gray-600">Manage and track your uploaded documents</p>
                    </div>
                    
                    <!-- Upload Button -->
                    <a href="{{ route('employee.documents.upload') }}" 
                       class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition hover:shadow-md">
                        <i class="fas fa-plus mr-2"></i> Upload New Document
                    </a>
                </div>

                @if($documents->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">No documents yet</h3>
                        <p class="text-gray-500 mb-4">Upload your first document to get started</p>
                        <a href="{{ route('employee.documents.upload') }}" 
                           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-2"></i> Upload Document
                        </a>
                    </div>
                @else
                    <!-- Documents Table -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left p-4 font-medium text-gray-600">Document</th>
                                        <th class="text-left p-4 font-medium text-gray-600">Uploaded</th>
                                        <th class="text-left p-4 font-medium text-gray-600">Status</th>
                                        <th class="text-right p-4 font-medium text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($documents as $document)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="p-4">
                                                <div class="font-medium text-gray-800">
                                                    @foreach(App\Models\Document::DOCUMENT_TYPES as $category => $types)
                                                        @if(array_key_exists($document->document_type, $types))
                                                            {{ $types[$document->document_type] }}
                                                            @break
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    {{ basename($document->file_path) }}
                                                </div>
                                            </td>
                                            <td class="p-4 text-gray-600">
                                                {{ $document->created_at->format('M d, Y') }}
                                                <div class="text-sm text-gray-400">
                                                    {{ $document->created_at->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                    {{ $document->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                                    ($document->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($document->status) }}
                                                </span>
                                            </td>
                                            <td class="p-4">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('protected.files', [
                                                        'user_id' => auth()->user()->user_id,
                                                        'filename' => basename($document->file_path)
                                                    ]) }}" 
                                                       target="_blank"
                                                       class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition"
                                                       title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('protected.files.download', [
                                                        'user_id' => auth()->user()->user_id,
                                                        'filename' => basename($document->file_path)
                                                    ]) }}" 
                                                       class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-full transition"
                                                       title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    
                                                    <button type="button" 
                                                            onclick="confirmDelete('{{ route('employee.documents.destroy', $document->id) }}')"
                                                            class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-full transition"
                                                            title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Delete Document?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    form.appendChild(csrf);
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    form.appendChild(method);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
    
    <!-- SweetAlert for better confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>