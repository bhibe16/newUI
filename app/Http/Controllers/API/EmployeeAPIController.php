<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmployeeAPIController extends Controller
{
    public function index()
    {
        try {
            $response = Http::withoutVerifying()->get('https://hr1.gwamerchandise.com/api/employee');
            
            if (!$response->successful()) {
                Log::error('API Request Failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return redirect()->back()->with('error', 'Failed to fetch employees. Status: ' . $response->status());
            }

            $employees = $response->json();
            
            if (!is_array($employees)) {
                throw new \Exception('Invalid API response format');
            }

            // Get statuses from cache or initialize empty array
            $statuses = Cache::get('employee_statuses', []);

            // Add status to each employee
            foreach ($employees as &$employee) {
                $employee['status'] = $statuses[$employee['id']] ?? 'pending';
            }

            // Calculate counts
            $pendingCount = count(array_filter($employees, fn($e) => ($e['status'] ?? 'pending') === 'pending'));
            $approvedCount = count(array_filter($employees, fn($e) => ($e['status'] ?? 'pending') === 'approved'));
            $rejectedCount = count(array_filter($employees, fn($e) => ($e['status'] ?? 'pending') === 'rejected'));

            return view('admin.newhiredemp.index', [
                'employees' => $employees,
                'total' => count($employees),
                'pendingCount' => $pendingCount,
                'approvedCount' => $approvedCount,
                'rejectedCount' => $rejectedCount
            ]);

        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching data: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $employeeId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        try {
            // Get current statuses
            $statuses = Cache::get('employee_statuses', []);
            
            // Update status
            $statuses[$employeeId] = $request->status;
            
            // Save back to cache
            Cache::put('employee_statuses', $statuses, now()->addDays(30));

            // Calculate counts from the updated statuses
            $pending = count(array_filter($statuses, fn($s) => $s === 'pending'));
            $approved = count(array_filter($statuses, fn($s) => $s === 'approved'));
            $rejected = count(array_filter($statuses, fn($s) => $s === 'rejected'));

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'counts' => [
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'total' => count($statuses)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Status Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }
}