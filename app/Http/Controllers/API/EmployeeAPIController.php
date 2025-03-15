<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmployeeAPIController extends Controller
{
    public function index()
    {
        try {
            // Make the API request
            $response = Http::withoutVerifying()->get('https://hr1.gwamerchandise.com/api/employee');
            
            // Check if the request was successful
            if (!$response->successful()) {
                Log::error('API Request Failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return redirect()->back()->with('error', 'Failed to fetch employees. Status: ' . $response->status());
            }

            // Decode the JSON response
            $employees = $response->json();
            
            // Validate the response structure
            if (!is_array($employees)) {
                throw new \Exception('Invalid API response format');
            }

            // Return the view with the employee data
            return view('admin.newhiredemp.index', [
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            Log::error('API Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching data: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $employeeId)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:pending,approved,reject'
        ]);

        // Update logic here (this is where you'd call your API)
        // For demo purposes, we'll just redirect back
        return redirect()->back()
            ->with('success', 'Status updated successfully')
            ->withInput();
    }
}