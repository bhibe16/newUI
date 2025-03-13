<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get total counts
        $totalEmployees = Employee::count();
        $totalDepartments = Department::count();
        $totalPositions = Position::count(); // Count total rows in the positions table
        
        // Fetch all departments and positions
        $departments = Department::all();
        $positions = Position::all(); // Fetch all positions
        // Count status occurrences
        $statusCounts = Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Pass the data to the view
        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'totalDepartments' => $totalDepartments,
            'totalPositions' => $totalPositions,
            'departments' => $departments,
            'positions' => $positions, // Fix: Now passing positions
            'statusCounts' => $statusCounts,
        ]);
    }
}
