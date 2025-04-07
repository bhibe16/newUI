<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Employee History</title>
     @production
    <link rel="stylesheet" href="{{ asset('build/assets/style-Wg8zdAtV.css') }}">
    <script type="module" src="{{ asset('build/assets/app-LM_T2gVS.js') }}"></script>
@else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endproduction
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="main-content min-h-screen bg-gray-100">

    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-6 sm:p-12">
            <div class="bg-white p-6 rounded-lg shadow-lg text-gray-700 max-w-4xl mx-auto">
                <h1 class="text-2xl font-semibold text-gray-800 mb-4">My Documents</h1>

                <!-- Upload Button -->
                <div class="flex justify-end mb-4">
                    <a href="{{ route('employee.documents.upload') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i> Upload New Document
                    </a>
                </div>

                @if($documents->isEmpty())
                    <p class="text-gray-500 text-center py-4">No documents found.</p>
                @else
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full border-collapse">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="text-left p-3">Document Name</th>
                                    <th class="text-left p-3">Uploaded Date</th>
                                    <th class="text-left p-3">Status</th>
                                    <th class="text-left p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach($documents as $document)
                                    <tr class="border-b hover:bg-gray-100 transition">
                                        <td class="p-3">
                                            @foreach(App\Models\Document::DOCUMENT_TYPES as $category => $types)
                                                @if(array_key_exists($document->document_type, $types))
                                                    {{ $types[$document->document_type] }}
                                                    @break
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="p-3">{{ $document->created_at->format('M d, Y') }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 rounded text-white text-sm 
                                                {{ $document->status == 'approved' ? 'bg-green-500' : 
                                                ($document->status == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
                                                {{ ucfirst($document->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex space-x-2">
                                            <a href="{{ route('protected.files', [
    'user_id' => auth()->user()->user_id,
    'filename' => basename($document->file_path)
]) }}" 
   target="_blank"
   class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition"
   title="View">
   <i class="fas fa-eye"></i>
</a>
                                                <!-- For downloading -->
                                                <a href="{{ route('protected.files.download', [
    'user_id' => auth()->user()->user_id,
    'filename' => basename($document->file_path)
]) }}" 
   class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition"
   title="Download">
   <i class="fas fa-download"></i>
</a>
                                                <form action="{{ route('employee.documents.destroy', $document->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            onclick="confirmDelete(this.form)"
                                                            class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition"
                                                            title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(form) {
            if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
                form.submit();
            }
        }
    </script>
</body>
</html>