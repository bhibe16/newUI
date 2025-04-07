<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Upload Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Completely remove all shadows and default styling from select */
        select.custom-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            box-shadow: none !important;
            outline: none !important;
            background-image: none;
        }
        
        /* Custom select container */
        .select-container {
            position: relative;
        }
        .select-container::after {
            content: "\f078";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6b7280;
        }
        
        /* File upload container */
        .file-upload-container {
            position: relative;
            min-height: 120px;
        }
        
        /* Preview container */
        #file-preview-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
        }
        
        /* Upload area styling */
        #file-upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <div class="flex flex-grow">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow p-4 md:p-8">
            <div class="max-w-2xl mx-auto">
                <!-- Upload Card -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm">
                    <!-- Header with back button -->
                    <div class="flex items-center mb-6">
                        <a href="{{ url()->previous() }}" class="mr-4 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Upload New Document</h1>
                            <p class="text-gray-500 text-sm">Complete the form to submit your document</p>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('employee.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Document Type -->
                        <div class="select-container">
                            <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Document Type <span class="text-red-500">*</span>
                            </label>
                            <select name="document_type" id="document_type" required
                                    class="custom-select w-full px-4 py-3 border border-gray-300 rounded-lg bg-white">
                                <option value="" disabled selected>Select a document type</option>
                                @foreach(App\Models\Document::DOCUMENT_TYPES as $category => $types)
                                    <optgroup label="{{ ucfirst(str_replace('_', ' ', $category)) }}">
                                        @foreach($types as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Document File <span class="text-red-500">*</span>
                            </label>
                            <div class="file-upload-container mt-1 border-2 border-gray-300 border-dashed rounded-lg">
                                <div id="file-upload-area">
                                    <div class="space-y-1 text-center h-full flex flex-col justify-center">
                                        <div class="flex justify-center text-gray-400">
                                            <i class="fas fa-cloud-upload-alt text-4xl"></i>
                                        </div>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="text-blue-600 font-medium">Upload a file</span>
                                            <span class="pl-1">or drag and drop</span>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PDF, JPG, PNG up to 10MB
                                        </p>
                                    </div>
                                </div>
                                <div id="file-preview-container">
                                    <i class="fas fa-file-alt text-3xl text-blue-500 mb-2"></i>
                                    <p id="file-name" class="text-sm font-medium text-gray-900 break-all max-w-full px-2"></p>
                                    <p id="file-size" class="text-xs text-gray-500 mt-1"></p>
                                    <button type="button" id="remove-file" class="mt-3 text-xs text-red-500 hover:text-red-700 focus:outline-none">
                                        Remove file
                                    </button>
                                </div>
                                <input id="document_file" name="document_file" type="file" required
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                            <a href="{{ url()->previous() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center transition">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                                <i class="fas fa-upload"></i>
                                <span>Upload Document</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Help Section -->
                <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Need help uploading?</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>• Ensure your document is clear and legible</p>
                                <p>• File size should not exceed 10MB</p>
                                <p>• Accepted formats: PDF, JPG, PNG</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // File upload elements
            const fileInput = document.getElementById('document_file');
            const fileUploadArea = document.getElementById('file-upload-area');
            const filePreviewContainer = document.getElementById('file-preview-container');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const removeFileBtn = document.getElementById('remove-file');
            const dropZone = document.querySelector('.file-upload-container');

            // Initialize by hiding preview and showing upload area
            fileUploadArea.style.display = 'flex';
            filePreviewContainer.style.display = 'none';

            // Handle file selection
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length) {
                    showFilePreview(this.files[0]);
                }
            });

            // Remove file handler - FIXED THIS FUNCTION
            removeFileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = ''; // Clear the file input
                fileUploadArea.style.display = 'flex'; // Show upload area
                filePreviewContainer.style.display = 'none'; // Hide preview
            });

            // Drag and drop handlers
            ['dragover', 'dragenter'].forEach(event => {
                dropZone.addEventListener(event, function(e) {
                    e.preventDefault();
                    this.classList.add('border-blue-500', 'bg-blue-50');
                });
            });

            ['dragleave', 'dragend'].forEach(event => {
                dropZone.addEventListener(event, function(e) {
                    e.preventDefault();
                    this.classList.remove('border-blue-500', 'bg-blue-50');
                });
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-500', 'bg-blue-50');
                
                if (e.dataTransfer.files && e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    showFilePreview(e.dataTransfer.files[0]);
                }
            });

            // Show file preview
            function showFilePreview(file) {
                if (!file) return;
                
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                
                fileUploadArea.style.display = 'none';
                filePreviewContainer.style.display = 'flex';
            }

            // Format file size
            function formatFileSize(bytes) {
                if (typeof bytes !== 'number' || isNaN(bytes)) return '';
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }
        });
    </script>
</body>
</html>