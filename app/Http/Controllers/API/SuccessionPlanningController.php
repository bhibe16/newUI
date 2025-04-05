<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SuccessionPlanningController extends Controller
{
    public function index()
    {
        $token = 'API TOKEN HERE'; // Replace with your actual token
    
        $response = Http::withToken($token)
            ->withoutVerifying()
            ->get('https://hr2.gwamerchandise.com/api/employeeEvaluation.php'); // Ensure this is the correct API endpoint
    
        if ($response->successful()) {
            $successionplanning = $response->json(); // Rename variable to match view
            return view('admin.successionplanning.index', compact('successionplanning'));
        } else {
            return back()->with('error', 'Failed to fetch succession planning data.');
        }
    }    
}