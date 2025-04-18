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
            $activeUsers = Employee::where('employment_status', 'active')->count();
            $inactiveUsers = Employee::where('employment_status', 'inactive')->count();
            $onleaveUsers = Employee::where('employment_status', 'onleave')->count();
            
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

            // Get recent employees (last 5) with department and position relationships
            $recentEmployees = Employee::with(['department', 'position'])
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 items per page


            // Ensure the current year is consistent across all queries
            $currentYear = now()->year;
            
            // 📅 Monthly hires for current year
            $monthlyStartDates = Employee::selectRaw('MONTH(start_date) as month, COUNT(*) as count')
                ->whereNotNull('start_date')
                ->whereYear('start_date', $currentYear)
                ->groupBy(DB::raw('MONTH(start_date)'))
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
            
            // Ensure all 12 months are represented (fill missing with 0)
            $monthlyStartDatesData = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyStartDatesData[$i] = $monthlyStartDates[$i] ?? 0;
            }
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            // 📆 Yearly hires (all years)
            $yearlyStartDates = Employee::selectRaw('YEAR(start_date) as year, COUNT(*) as count')
                ->whereNotNull('start_date')
                ->groupBy(DB::raw('YEAR(start_date)'))
                ->orderBy('year', 'asc')
                ->pluck('count', 'year')
                ->toArray();
            
            // 🧭 Quarterly hires for current year
            $quarterlyStartDates = Employee::selectRaw('QUARTER(start_date) as quarter, COUNT(*) as count')
                ->whereNotNull('start_date')
                ->whereYear('start_date', $currentYear)
                ->groupBy(DB::raw('QUARTER(start_date)'))
                ->orderBy('quarter')
                ->pluck('count', 'quarter')
                ->toArray();
            
            // Ensure all 4 quarters are represented (fill missing with 0)
            $quarterlyStartDatesData = [];
            for ($i = 1; $i <= 4; $i++) {
                $quarterlyStartDatesData[$i] = $quarterlyStartDates[$i] ?? 0;
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
                'recentEmployees' => $recentEmployees, // Add recent employees to the view data
                'activeUsers' => $activeUsers,
                'inactiveUsers' => $inactiveUsers,
                'onleaveUsers' => $onleaveUsers,
                'monthlyStartDates' => array_values($monthlyStartDatesData),
                'chartMonthLabels' => $monthNames, // Changed to 'chartMonthLabels'
                'yearlyStartDates' => $yearlyStartDates,
                'quarterlyStartDates' => array_values($quarterlyStartDatesData),
            ]);
        }

        // API endpoint for employee records pagination
        public function getEmployeeRecords(Request $request)
        {
            $perPage = $request->input('per_page', 5);
            $page = $request->input('page', 1);
            
            $employees = Employee::with(['department', 'position'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
            
            return response()->json($employees);
        }
    }