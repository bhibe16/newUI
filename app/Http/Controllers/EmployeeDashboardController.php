<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        $employeeRecord = Employee::where('user_id', $userId)
            ->with(['department', 'position'])
            ->first();

        $documentStatus = $this->getDocumentStatus($userId);
        
        return view('employee.dashboard', [
            'employeeRecord' => $employeeRecord,
            'recordComplete' => (bool)$employeeRecord,
            'uploadedDocuments' => $documentStatus['uploaded'],
            'pendingDocuments' => $documentStatus['pending'],
            'requiredDocumentTypes' => $documentStatus['requiredTypes'],
            'documentCompletionPercentage' => $documentStatus['completionPercentage'],
            'uploadedDocumentsCount' => $documentStatus['uploadedCount'],
            'documentTypeNames' => $this->getDocumentTypeNames() // Add this line
        ]);
    }

    protected function getDocumentStatus($userId)
    {
        // Database values (must match exactly what's stored)
        $requiredTypes = [
            'resume',
            'government_id',
            'police_clearance',
            'tin_sss_pagibig_philhealth'
        ];

        $uploadedDocuments = Document::where('user_id', $userId)
            ->where('status', '!=', 'rejected')
            ->get();

        $uploadedTypes = $uploadedDocuments->pluck('document_type')->unique()->toArray();
        $pendingDocuments = array_diff($requiredTypes, $uploadedTypes);

        $totalRequired = count($requiredTypes);
        $completedRequired = $totalRequired - count($pendingDocuments);
        $completionPercentage = $totalRequired > 0 ? round(($completedRequired / $totalRequired) * 100) : 0;

        return [
            'uploaded' => $uploadedDocuments,
            'pending' => $pendingDocuments,
            'requiredTypes' => $requiredTypes,
            'completionPercentage' => $completionPercentage,
            'uploadedCount' => $uploadedDocuments->count()
        ];
    }

    protected function getDocumentTypeNames()
    {
        return [
            'resume' => 'Resume/CV',
            'government_id' => 'Government ID',
            'police_clearance' => 'Police Clearance',
            'tin_sss_pagibig_philhealth' => 'TIN / SSS / Pag - IBIG / PhilHealth'
        ];
    }
}