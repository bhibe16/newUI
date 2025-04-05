<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get total counts
        $totalEmployees = Employee::count();
        $totalDepartments = Department::count();
        $totalPositions = Position::count();
        $totalUsers = User::count();
        $employeeUsers = User::where('role', 'employee')->count();
        
        // Fetch all departments and positions WITH employee counts
        $departments = Department::withCount('employees')->get();
        $positions = Position::withCount('employees')->get();
        
        // Count status occurrences
        $statusCounts = Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
    
        // Get new hires data (last 6 months)
        $newHires = Employee::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();
    
        // Prepare chart data
        $chartData = [];
        $labels = [];
        
        // Generate data for each of the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths(5 - $i);
            $month = $date->month;
            $year = $date->year;
            $labels[] = $date->format('M Y');
            
            $matchingMonth = $newHires->first(function ($item) use ($month, $year) {
                return $item->month == $month && $item->year == $year;
            });
            
            $chartData[] = $matchingMonth ? $matchingMonth->count : 0;
        }
    
        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'totalDepartments' => $totalDepartments,
            'totalPositions' => $totalPositions,
            'departments' => $departments,
            'positions' => $positions,
            'statusCounts' => $statusCounts,
            'employeeUsers' => $employeeUsers,
            'monthlyHires' => $chartData,
            'monthNames' => $labels,
        ]);
    }
}