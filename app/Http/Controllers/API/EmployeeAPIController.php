<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmployeeAPIController extends Controller
{
    public function index(Request $request)
{
    try {
        $searchTerm = $request->input('search');
        
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

        // Filter employees if search term exists
        if ($searchTerm) {
            $employees = array_filter($employees, function($employee) use ($searchTerm) {
                $searchable = [
                    strtolower($employee['first_name'] ?? ''),
                    strtolower($employee['last_name'] ?? ''),
                    strtolower($employee['job_position'] ?? ''),
                    strtolower($employee['department'] ?? ''),
                    strtolower($employee['email'] ?? ''),
                    strtolower($employee['id'] ?? '')
                ];
                
                foreach ($searchable as $field) {
                    if (str_contains($field, strtolower($searchTerm))) {
                        return true;
                    }
                }
                return false;
            });
            
            $employees = array_values($employees); // Reset array keys
        }

        // Get statuses from cache
        $statuses = Cache::get('employee_statuses', []);

        // Add status to each employee
        foreach ($employees as &$employee) {
            $employee['status'] = $statuses[$employee['id']] ?? 'pending';
        }

        // Pagination
        $currentPage = $request->get('page', 1);
        $perPage = 7;
        $offset = ($currentPage - 1) * $perPage;
        $paginatedEmployees = array_slice($employees, $offset, $perPage);
        $totalPages = ceil(count($employees) / $perPage);

        // Calculate counts
        $pendingCount = count(array_filter($employees, fn($e) => ($e['status'] ?? 'pending') === 'pending'));
        $approvedCount = count(array_filter($employees, fn($e) => ($e['status'] ?? 'pending') === 'approved'));
        $rejectedCount = count(array_filter($employees, fn($e) => ($e['status'] ?? 'pending') === 'rejected'));

        return view('admin.newhiredemp.index', [
            'employees' => $employees,
            'paginatedEmployees' => $paginatedEmployees,
            'total' => count($employees),
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'searchTerm' => $searchTerm
        ]);

    } catch (\Exception $e) {
        Log::error('API Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error fetching data: ' . $e->getMessage());
    }
}
}