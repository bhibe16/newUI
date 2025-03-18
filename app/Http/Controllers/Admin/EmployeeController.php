<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmploymentHistory;
use App\Models\EducationalHistory;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $status = $request->input('status');
    
        // Ensure status is valid
        $validStatuses = ['approved', 'reject', 'pending'];
        if ($status && !in_array($status, $validStatuses)) {
            $status = null; 
        }
    
        // Search with Meilisearch (AI-powered)
        $employees = Employee::search($query)->get();
    
        // Filter results by status if needed
        if ($status) {
            $employees = $employees->where('status', $status);
        }
    
        return view('admin.employees.index', ['employees' => $employees]);
    }

    
    public function apiIndex()
    {
        $employees = Employee::all();
    
        return response()->json([
            'employees' => $employees
        ], 200);
    }

    
    

    public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    $employeeName = $employee->first_name . ' ' . $employee->last_name; // Store name before deleting
    $employee->delete(); // Soft delete

    return redirect()->route('admin.employees.archived')
        ->with('success', 'Employee ' . $employeeName . ' deleted successfully.');
}

public function archived()
{
    $employees = Employee::onlyTrashed()->paginate(10);
    return view('admin.employees.archived', compact('employees'));
}

public function restore($id)
{
    $employee = Employee::onlyTrashed()->findOrFail($id);
    $employee->restore();

    return redirect()->route('admin.employees.index')
        ->with('success', 'Employee ' . $employee->first_name . ' ' . $employee->last_name . ' restored successfully.');
}


    public function dashboard()
    {
        // Fetch employee-specific data
        $employee = auth()->user(); // Get the authenticated employee
        $records = $employee->records; // Assuming you have a relationship defined

        return view('employees.dashboard', compact('employees', 'records'));
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:approved,pending,reject'
    ]);

    $employee = Employee::findOrFail($id);
    $employee->status = $request->status;
    $employee->save();

    return response()->json(['success' => true]);
}


}
