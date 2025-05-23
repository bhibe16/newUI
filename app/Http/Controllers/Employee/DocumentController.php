<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;


class DocumentController extends Controller
{
    // Display the document upload form
    public function showForm()
    {
        return view('employee.documents.upload');
    }

    // Store a newly created document
    public function store(Request $request)
{
    $request->validate([
        'document_type' => 'required|string|max:255',
        'document_file' => 'required|file|mimes:pdf,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('document_file') && $request->file('document_file')->isValid()) {
        $file = $request->file('document_file');
        $path = $file->storeAs('documents', time() . '-' . $file->getClientOriginalName(), 'public');

        $employee = Employee::where('user_id', auth()->user()->user_id)->first();

        if (!$employee) {
            return back()->with('error', 'Employee record not found.');
        }

        $document = Document::create([
            'user_id' => $employee->user_id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'document_type' => $request->document_type,
            'file_path' => $path,
            'status' => 'pending',
        ]);

        // Notify admin and HR3 users
        $adminUsers = \App\Models\User::whereIn('role', ['admin', 'hr3'])->get();
        \Notification::send($adminUsers, new \App\Notifications\DocumentUploaded($document));

        return redirect()->route('employee.documents.index')->with('success', 'Document uploaded successfully!');
    }

    return back()->with('error', 'Failed to upload the document.');
}


    // View all documents uploaded by the employee
    public function index()
    {
        $documents = Document::where('user_id', auth()->user()->user_id)->get(); // ✅ Use custom user_id
        return view('employee.documents.index', compact('documents'));
    }

    // Delete a document
    public function destroy($id)
{
    $document = Document::where('user_id', auth()->user()->user_id)
        ->findOrFail($id);

    // Delete the file from storage
    Storage::disk('public')->delete($document->file_path);

    // Delete the document record
    $document->delete();

    return redirect()->route('employee.documents.index')
        ->with('success', 'Document deleted successfully');
}

public function viewFile($user_id, $filename)
{
    // Verify authentication
    if (!auth()->check()) {
        abort(403, 'Unauthorized access');
    }

    // Verify ownership
    if (auth()->user()->user_id != $user_id) {
        abort(403, 'You do not have permission to access this file');
    }

    // Find the document record
    $document = Document::where('user_id', $user_id)
                        ->where('file_path', 'like', '%'.$filename)
                        ->firstOrFail();

    // Get the full file path
    $filePath = storage_path('app/public/' . $document->file_path);

    // Check if file exists
    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }

    // Return the file with appropriate headers
    return response()->file($filePath, [
        'Content-Type' => mime_content_type($filePath),
        'Content-Disposition' => 'inline; filename="'.basename($filePath).'"'
    ]);
}

public function downloadFile($user_id, $filename)
{
    // Same verification as viewFile
    if (!auth()->check() || auth()->user()->user_id != $user_id) {
        abort(403);
    }

    $document = Document::where('user_id', $user_id)
                        ->where('file_path', 'like', '%'.$filename)
                        ->firstOrFail();

    $filePath = storage_path('app/public/' . $document->file_path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->download($filePath);
}

}
