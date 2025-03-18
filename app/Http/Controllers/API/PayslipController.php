<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
class PayslipController extends Controller
{
    public function index()
    {
        $token = 'API TOKEN HERE'; // Replace with your actual token
        
        $response = Http::withToken($token)
            ->withoutVerifying()
            ->get('https://hr4.gwamerchandise.com/pages/api/payslip_api.php'); // Update API endpoint for payslips

        if ($response->successful()) {
            $payslips = $response->json(); // Convert API response to an array
            return view('admin.payslip.index', compact('payslips')); // Pass data to Blade view
        } else {
            return back()->with('error', 'Failed to fetch payslip data.');
        }
    }
    public function bonuses()
    {
        $token = 'API TOKEN HERE'; // Replace with your actual token
        
        $response = Http::withToken($token)
            ->withoutVerifying()
            ->get('https://hr4.gwamerchandise.com/pages/api/payslip_api.php'); // Update API endpoint for payslips

        if ($response->successful()) {
            $payslips = $response->json(); // Convert API response to an array
            return view('admin.payslip.bonuses', compact('payslips')); // Pass data to Blade view
        } else {
            return back()->with('error', 'Failed to fetch payslip data.');
        }
    }
    public function deduction()
    {
        $token = 'API TOKEN HERE'; // Replace with your actual token
        
        $response = Http::withToken($token)
            ->withoutVerifying()
            ->get('https://hr4.gwamerchandise.com/pages/api/payslip_api.php'); // Update API endpoint for payslips

        if ($response->successful()) {
            $payslips = $response->json(); // Convert API response to an array
            return view('admin.payslip.deduction', compact('payslips')); // Pass data to Blade view
        } else {
            return back()->with('error', 'Failed to fetch payslip data.');
        }
    }
    public function downloadPayslip(Request $request)
    {

        $token = 'API TOKEN HERE'; // Replace with your actual token
        
        $response = Http::withToken($token)
            ->withoutVerifying()
            ->get('https://hr4.gwamerchandise.com/pages/api/payslip_api.php'); // Update API endpoint for payslips

        if ($response->successful()) {
            \Log::info('Payslip API Response:', $response->json()); // Check response content
        }
        $payslipData = [
            'employee_name' => $request->query('employee_name', 'N/A'),
            'employee_id' => $request->query('employee_id', 'N/A'),
            'department' => $request->query('department', 'N/A'),
            'employer' => $request->query('employer', 'N/A'),
            'contact' => $request->query('contact', 'N/A'),
            'pay_period' => $request->query('pay_period', 'N/A'),
            'payment_date' => $request->query('payment_date', 'N/A'),
            'base_salary' => $request->query('base_salary', 'N/A'),
            'overtime' => $request->query('overtime', 'N/A'),
            'bonuses' => $request->query('bonuses', 'N/A'),
            'gross_earnings' => $request->query('gross_earnings', 'N/A'),
            'income_tax' => $request->query('income_tax', 'N/A'),
            'social_security' => $request->query('social_security', 'N/A'),
            'pension' => $request->query('pension', 'N/A'),
            'health_insurance' => $request->query('health_insurance', 'N/A'),
            'net_pay' => $request->query('net_pay', 'N/A'),
            'total_earnings' => $request->query('total_earnings', 'N/A'),
            'total_deductions' => $request->query('total_deductions', 'N/A')
        ];
        
    
        // Check if values exist
        \Log::info('Payslip Data:', $payslipData);
    
        $pdf = Pdf::loadView('admin.payslip.pdf', compact('payslipData'));
    
        return $pdf->download('payslip.pdf');
    }

    public function downloadbonuses(Request $request)
    {
        $token = 'API TOKEN HERE'; // Replace with your actual token
        
        $response = Http::withToken($token)
            ->withoutVerifying()
            ->get('https://hr4.gwamerchandise.com/pages/api/payslip_api.php'); // Update API endpoint for payslips

        if ($response->successful()) {
            \Log::info('Payslip API Response:', $response->json()); // Check response content
        }
        // Create the data array to pass to the PDF view
        $data = [
           'employee_name' => $request->query('employee_name', 'N/A'),
            'employee_id' => $request->query('employee_id', 'N/A'),
            'department' => $request->query('department', 'N/A'),
            'employer' => $request->query('employer', 'N/A'),
            'contact' => $request->query('contact', 'N/A'),
            'pay_period' => $request->query('pay_period', 'N/A'),
            'payment_date' => $request->query('payment_date', 'N/A'),
            'base_salary' => $request->query('base_salary', 'N/A'),
            'overtime' => $request->query('overtime', 'N/A'),
            'bonuses' => $request->query('bonuses', 'N/A'),
            'gross_earnings' => $request->query('gross_earnings', 'N/A'),
            'income_tax' => $request->query('income_tax', 'N/A'),
            'social_security' => $request->query('social_security', 'N/A'),
            'pension' => $request->query('pension', 'N/A'),
            'health_insurance' => $request->query('health_insurance', 'N/A'),
            'net_pay' => $request->query('net_pay', 'N/A'),
            'total_earnings' => $request->query('total_earnings', 'N/A'),
            'total_deductions' => $request->query('total_deductions', 'N/A')
        ];

        \Log::info('Data:', $data);

        // Generate the PDF (assuming you have a Blade view for this)
        $pdf = PDF::loadView('admin.payslip.pdfbns', compact('data'));

        // Return the PDF as a download
        return $pdf->download('bonus.pdf');
    }
    
}
