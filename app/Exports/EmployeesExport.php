<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $query = Employee::with(['department', 'position'])
            ->select('user_id', 'first_name', 'last_name', 'email', 'employment_status', 'department_id', 'position_id', 'created_at');
            
        if ($this->status) {
            $query->where('employment_status', $this->status);
        }
        
        return $query->get()->map(function ($employee) {
            return [
                'ID' => $employee->user_id,
                'First Name' => $employee->first_name,
                'Last Name' => $employee->last_name,
                'Email' => $employee->email,
                'Department' => $employee->department->name ?? 'N/A',
                'Position' => $employee->position->name ?? 'N/A',
                'Status' => $employee->employment_status,
                'Created At' => $employee->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Department',
            'Position',
            'Status',
            'Created At'
        ];
    }
}