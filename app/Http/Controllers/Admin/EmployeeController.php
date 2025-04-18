<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmploymentHistory;
use App\Models\EducationalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $status = $request->input('status');
        $dateRange = $request->input('date_range');
        $startDate = $request->input('start_date'); // Add this line
    
        // Initialize the employee query with relationships
        $employeeQuery = Employee::query()->with(['department', 'position']);
    
        // Check for status in search query if not provided as parameter
        $validStatuses = ['approved', 'reject', 'pending'];
        if (!$status && $query) {
            foreach ($validStatuses as $validStatus) {
                if (strtolower($query) === $validStatus) {
                    $status = $validStatus;
                    $query = null;
                    break;
                }
            }
        }
    
        // Apply status filter if provided (either from input or detected from search)
        if ($status && in_array($status, $validStatuses)) {
            $employeeQuery->where('status', $status);
        }
    
        // Handle date range filtering
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $startDate = date('Y-m-d', strtotime(trim($dates[0])));
                $endDate = date('Y-m-d', strtotime(trim($dates[1])));
                $employeeQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Add start_date filtering
        if ($startDate) {
            $employeeQuery->whereDate('created_at', '>=', date('Y-m-d', strtotime($startDate)));
        }
    
        // Check for NLP-style query parsing
        $nlpFilters = $this->parseNaturalLanguageQuery($query);
    
        if ($nlpFilters) {
            // Department filtering
            if (isset($nlpFilters['department'])) {
                $this->applyDepartmentFilter($employeeQuery, $nlpFilters['department']);
            }
    
            // Position filtering
            if (isset($nlpFilters['position'])) {
                $this->applyPositionFilter($employeeQuery, $nlpFilters['position']);
            }
            
            // Name filtering
            if (isset($nlpFilters['name'])) {
                $this->applyNameFilter($employeeQuery, $nlpFilters['name']);
            }
            
            // Email filtering
            if (isset($nlpFilters['email'])) {
                $employeeQuery->where('email', 'like', '%' . $nlpFilters['email'] . '%');
            }
            
            // ID filtering
            if (isset($nlpFilters['id'])) {
                $employeeQuery->where('user_id', 'like', '%' . $nlpFilters['id'] . '%');
            }
            
            // Skill filtering
            if (isset($nlpFilters['skills'])) {
                $this->applySkillFilter($employeeQuery, $nlpFilters['skills']);
            }
        } elseif ($query) {
            // Advanced keyword search with ranking
            $employeeQuery->where(function ($q) use ($query) {
                $keywords = explode(' ', $query);
                
                foreach ($keywords as $keyword) {
                    if (strlen($keyword) > 2) { // Only search for meaningful keywords
                        $q->orWhere(function ($subQuery) use ($keyword) {
                            // Basic fields
                            $subQuery->where('first_name', 'like', "%$keyword%")
                                   ->orWhere('last_name', 'like', "%$keyword%")
                                   ->orWhere('user_id', 'like', "%$keyword%")
                                   ->orWhere('email', 'like', "%$keyword%");
                            
                            // Department and position relationships
                            $subQuery->orWhereHas('department', function ($deptQuery) use ($keyword) {
                                $deptQuery->where('name', 'like', "%$keyword%");
                            })
                            ->orWhereHas('position', function ($posQuery) use ($keyword) {
                                $posQuery->where('name', 'like', "%$keyword%");
                            });
                            
                            // Employment history
                            $subQuery->orWhereHas('employmentHistories', function ($histQuery) use ($keyword) {
                                $histQuery->where('company_name', 'like', "%$keyword%")
                                         ->orWhere('job_title', 'like', "%$keyword%");
                            });
                            
                            // Educational history
                            $subQuery->orWhereHas('educationalHistories', function ($eduQuery) use ($keyword) {
                                $eduQuery->where('institution', 'like', "%$keyword%")
                                         ->orWhere('degree', 'like', "%$keyword%");
                            });
                        });
                    }
                }
            });
            
            // Add relevance scoring for better result ordering
            $employeeQuery->orderBy(DB::raw("
                CASE 
                    WHEN first_name LIKE '{$query}%' THEN 1
                    WHEN last_name LIKE '{$query}%' THEN 2
                    WHEN CONCAT(first_name, ' ', last_name) LIKE '%{$query}%' THEN 3
                    WHEN email LIKE '{$query}%' THEN 4
                    WHEN user_id LIKE '{$query}%' THEN 5
                    ELSE 6
                END
            "), 'asc');
        }
    
        // Paginate results with preserved query parameters
        $employees = $employeeQuery->paginate(10)->appends($request->except('page'));
    
        // Fetch related histories more efficiently
        $employeeIds = $employees->pluck('user_id');
        $employment = EmploymentHistory::whereIn('user_id', $employeeIds)->get()->groupBy('user_id');
        $educational = EducationalHistory::whereIn('user_id', $employeeIds)->get()->groupBy('user_id');
    
        // Add status counts for dashboard overview
        $statusCounts = [
            'pending' => Employee::where('status', 'pending')->count(),
            'approved' => Employee::where('status', 'approved')->count(),
            'reject' => Employee::where('status', 'reject')->count(),
        ];
    
        return view('admin.employees.index', compact('employees', 'employment', 'educational', 'statusCounts'));
    }
    
    protected function applyDepartmentFilter($query, $departmentName)
    {
        if ($departmentName === 'Human Resources') {
            $department = Department::where('name', 'Human Resources')->first();
        } else {
            $normalized = $this->normalizeDepartment($departmentName);
            $department = Department::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($normalized))])->first();

            if (!$department) {
                if (strpos(strtolower($normalized), 'it') !== false || 
                    strpos(strtolower($normalized), 'information technology') !== false) {
                    $department = Department::where('name', 'IT (Information Technology)')->first();
                } else {
                    $department = Department::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($normalized) . '%'])->first();
                }
            }
        }

        if ($department) {
            $query->where('department_id', $department->id);
        }
    }
    
    protected function applyPositionFilter($query, $positionName)
    {
        $normalizedPosition = $this->normalizePosition($positionName);

        if (is_array($normalizedPosition)) {
            $query->whereHas('position', function ($q) use ($normalizedPosition) {
                $q->where(function($subQuery) use ($normalizedPosition) {
                    foreach ($normalizedPosition as $position) {
                        $subQuery->orWhere('name', 'like', "%$position%");
                    }
                });
            });
        } else {
            $position = Position::where('name', $normalizedPosition)->first();
            if ($position) {
                $query->where('position_id', $position->id);
            }
        }
    }
    
    protected function applyNameFilter($query, $name)
    {
        $nameParts = explode(' ', $name);
        
        if (count($nameParts) === 1) {
            // Single name - search in both first and last names
            $query->where(function($q) use ($name) {
                $q->where('first_name', 'like', "%$name%")
                  ->orWhere('last_name', 'like', "%$name%");
            });
        } else {
            // Multiple names - assume first is first name, last is last name
            $query->where('first_name', 'like', "%{$nameParts[0]}%")
                  ->where('last_name', 'like', "%{$nameParts[count($nameParts)-1]}%");
        }
    }
    
    protected function applySkillFilter($query, $skills)
    {
        $query->whereHas('skills', function($q) use ($skills) {
            if (is_array($skills)) {
                $q->whereIn('name', $skills);
            } else {
                $q->where('name', 'like', "%$skills%");
            }
        });
    }

    private function parseNaturalLanguageQuery($query)
    {
        if (empty($query)) return null;

        $query = strtolower(trim($query));
        $filters = [];

        // 1. Check for email pattern
        if (preg_match('/\b[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}\b/', $query, $matches)) {
            $filters['email'] = $matches[0];
            return $filters; // If we found an email, prioritize that
        }

        // 2. Check for ID pattern (e.g., "id:123" or "employee #456")
        if (preg_match('/(?:id|employee\s*#?)\s*[:]?\s*([a-z0-9]+)/i', $query, $matches)) {
            $filters['id'] = trim($matches[1]);
        }

        // 3. Check for name patterns
        $namePatterns = [
            '/\b(?:name|employee)\s*(?:is|of|:)?\s*([a-z][a-z\s]+)/i',
            '/^(?!.*(department|position|skill))([a-z][a-z\s]+)$/i'
        ];
        
        foreach ($namePatterns as $pattern) {
            if (preg_match($pattern, $query, $matches) && !isset($filters['name'])) {
                $name = trim($matches[1]);
                if (str_word_count($name) > 0 && strlen($name) > 2) {
                    $filters['name'] = $name;
                    break;
                }
            }
        }

        // 4. Department patterns
        $departmentPatterns = [
            '/\b(?:department|dept|team)\s*(?:is|of|:)?\s*(.+?)(?:\s*(?:employee|staff|with))?\b/i' => 1,
            '/\b(?:in|from|of)\s+(.+?)\s+(?:department|dept|team)\b/i' => 1,
            '/\b(?:hr|human\s?resources?|it|finance|marketing|sales)\b/i' => 0,
        ];
        
        foreach ($departmentPatterns as $pattern => $group) {
            if (preg_match($pattern, $query, $matches)) {
                $department = $group === 0 ? $matches[0] : trim($matches[$group]);
                if (!empty($department) && !isset($filters['department'])) {
                    $filters['department'] = $department;
                    break;
                }
            }
        }

        // 5. Position patterns
        $positionPatterns = [
            '/\b(?:position|role|job|title)\s*(?:is|as|:)?\s*(.+?)(?:\s*(?:employee|staff|in))?\b/i' => 1,
            '/\b(?:is|as)\s+(?:a\s+)?(.+?)\s*\b/i' => 1,
            '/\b(?:looking\s+for|searching\s+for|find|need|want)\s+(.+?)\s*(?:position|role|job)?\b/i' => 1,
            '/\b(manager|managers)\b/i' => 1,
        ];
        
        foreach ($positionPatterns as $pattern => $group) {
            if (preg_match($pattern, $query, $matches) && !isset($filters['position'])) {
                $position = trim($matches[$group]);
                if (strlen($position) > 2) {
                    $filters['position'] = $position;
                    break;
                }
            }
        }

        // 6. Skill patterns
        if (preg_match('/\b(?:skill|expertise|knows|proficient\s+in)\s*(?:is|in|:)?\s*(.+?)\b/i', $query, $matches)) {
            $skills = array_map('trim', explode(',', $matches[1]));
            $filters['skills'] = count($skills) > 1 ? $skills : $skills[0];
        }

        // 7. Combined queries (e.g., "John Doe from Marketing department")
        if (!isset($filters['department']) && preg_match('/\bfrom\s+(.+?)\s+(?:department|dept|team)\b/i', $query, $matches)) {
            $filters['department'] = trim($matches[1]);
        }

        // 8. Handle "and" in queries (e.g., "HR and Marketing")
        if (isset($filters['department']) && strpos($filters['department'], ' and ') !== false) {
            $depts = explode(' and ', $filters['department']);
            $filters['department'] = array_map('trim', $depts);
        }

        // 9. Add date patterns
        $datePatterns = [
            '/\b(?:hired|started|joined)\s+(?:after|since|from)\s+(.+)/i' => 1,
            '/\b(?:after|since)\s+(.+?)\s+(?:hire|start|join)/i' => 1,
        ];
        
        foreach ($datePatterns as $pattern => $group) {
            if (preg_match($pattern, $query, $matches) && !isset($filters['start_date'])) {
                $dateString = trim($matches[$group]);
                try {
                    $filters['start_date'] = date('Y-m-d', strtotime($dateString));
                    // Remove the date part from the original query
                    $query = preg_replace($pattern, '', $query);
                } catch (\Exception $e) {
                    // Invalid date format, skip
                }
            }
        }
        return empty($filters) ? null : $filters;
    }

    private function normalizeDepartment($department)
    {
        if (is_array($department)) {
            return array_map([$this, 'normalizeDepartment'], $department);
        }

        $department = strtolower(trim($department));
        
        // Remove common suffixes only if they appear at the end
        $department = preg_replace('/(\s*(departments?|depts?|teams?|staff|employees?|members?)\s*)$/i', '', $department);
        
        // Special handling for common department names
        $mappings = [
            'hr' => 'Human Resources',
            'human resources' => 'Human Resources',
            'humanresource' => 'Human Resources',
            'it' => 'IT (Information Technology)',
            'information technology' => 'IT (Information Technology)',
            'info tech' => 'IT (Information Technology)',
            'fin' => 'Finance',
            'acct' => 'Accounting',
            'mktg' => 'Marketing',
            'pr' => 'Public Relations',
            'r&d' => 'Research and Development',
            'cs' => 'Customer Service',
            'tech support' => 'Technical Support',
            'ops' => 'Operations',
            'prod' => 'Production',
            'eng' => 'Engineering',
            'dev' => 'Development',
        ];
        
        if (array_key_exists($department, $mappings)) {
            return $mappings[$department];
        }
        
        return ucwords($department);
    }

    private function normalizePosition($position)
    {
        if (is_array($position)) {
            return array_map([$this, 'normalizePosition'], $position);
        }

        $position = strtolower(trim($position));
        
        // Remove common suffixes
        $position = preg_replace('/\s*(positions?|roles?|jobs?|staff|employees?|titles?|people|members?)\s*$/i', '', $position);
        
        // Special handling for manager search
        if ($position === 'manager' || $position === 'managers') {
            return Position::where('name', 'like', '%manager%')
                          ->pluck('name')
                          ->toArray();
        }
        
        // Exact position mappings
        $exactMappings = [
            'purchasing manager' => 'Purchasing Manager',
            'procurement specialist' => 'Procurement Specialist',
            'buyer' => 'Buyer',
            'sourcing analyst' => 'Sourcing Analyst',
            'vendor relations coordinator' => 'Vendor Relations Coordinator',
            'sales manager' => 'Sales Manager',
            'account executive' => 'Account Executive',
            'business development representative' => 'Business Development Representative',
            'sales associate' => 'Sales Associate',
            'key account manager' => 'Key Account Manager',
            'marketing manager' => 'Marketing Manager',
            'digital marketing specialist' => 'Digital Marketing Specialist',
            'brand manager' => 'Brand Manager',
            'social media manager' => 'Social Media Manager',
            'market research analyst' => 'Market Research Analyst',
            'merchandising manager' => 'Merchandising Manager',
            'visual merchandiser' => 'Visual Merchandiser',
            'product manager' => 'Product Manager',
            'category manager' => 'Category Manager',
            'retail merchandiser' => 'Retail Merchandiser',
            'inventory control manager' => 'Inventory Control Manager',
            'stock analyst' => 'Stock Analyst',
            'warehouse coordinator' => 'Warehouse Coordinator',
            'supply chain analyst' => 'Supply Chain Analyst',
            'inventory planner' => 'Inventory Planner',
            'finance manager' => 'Finance Manager',
            'accountant' => 'Accountant',
            'financial analyst' => 'Financial Analyst',
            'accounts payable/receivable specialist' => 'Accounts Payable/Receivable Specialist',
            'payroll specialist' => 'Payroll Specialist',
            'hr manager' => 'HR Manager',
            'talent acquisition specialist' => 'Talent Acquisition Specialist',
            'training & development officer' => 'Training & Development Officer',
            'compensation & benefits analyst' => 'Compensation & Benefits Analyst',
            'hr generalist' => 'HR Generalist',
            'customer service manager' => 'Customer Service Manager',
            'call center representative' => 'Call Center Representative',
            'customer support specialist' => 'Customer Support Specialist',
            'client relations coordinator' => 'Client Relations Coordinator',
            'technical support representative' => 'Technical Support Representative',
            'it manager' => 'IT Manager',
            'software developer' => 'Software Developer',
            'network administrator' => 'Network Administrator',
            'cybersecurity analyst' => 'Cybersecurity Analyst',
            'it support specialist' => 'IT Support Specialist',
            'logistics manager' => 'Logistics Manager',
            'supply chain coordinator' => 'Supply Chain Coordinator',
            'transportation analyst' => 'Transportation Analyst',
            'warehouse supervisor' => 'Warehouse Supervisor',
            'fleet manager' => 'Fleet Manager'
        ];
        
        // First try exact match
        foreach ($exactMappings as $key => $value) {
            if ($position === $key) {
                return $value;
            }
        }
        
        // Handle common abbreviations and synonyms
        $synonyms = [
            'dev' => 'Software Developer',
            'developer' => 'Software Developer',
            'programmer' => 'Software Developer',
            'coder' => 'Software Developer',
            'se' => 'Software Developer',
            'swe' => 'Software Developer',
            'admin' => 'Network Administrator',
            'sysadmin' => 'Network Administrator',
            'netadmin' => 'Network Administrator',
            'security' => 'Cybersecurity Analyst',
            'infosec' => 'Cybersecurity Analyst',
            'secops' => 'Cybersecurity Analyst',
            'helpdesk' => 'IT Support Specialist',
            'desktop support' => 'IT Support Specialist',
            'tech support' => 'Technical Support Representative',
            'csr' => 'Customer Service Representative',
            'rep' => 'Customer Service Representative',
            'agent' => 'Customer Service Representative',
            'bd' => 'Business Development Representative',
            'bdr' => 'Business Development Representative',
            'ae' => 'Account Executive',
            'am' => 'Account Manager',
            'pm' => 'Product Manager',
            'hrbp' => 'HR Generalist',
            'recruiter' => 'Talent Acquisition Specialist',
            'ta' => 'Talent Acquisition Specialist',
            'l&d' => 'Training & Development Officer',
            'trainer' => 'Training & Development Officer',
            'c&b' => 'Compensation & Benefits Analyst',
            'payroll' => 'Payroll Specialist',
            'ap/ar' => 'Accounts Payable/Receivable Specialist',
            'accounting' => 'Accountant',
            'fa' => 'Financial Analyst',
            'fm' => 'Finance Manager',
            'scm' => 'Supply Chain Manager',
            'logistics' => 'Logistics Manager',
            'wh' => 'Warehouse Supervisor',
            'inventory' => 'Inventory Control Manager',
            'ic' => 'Inventory Control Manager',
            'vm' => 'Visual Merchandiser',
            'cm' => 'Category Manager',
            'brand' => 'Brand Manager',
            'smm' => 'Social Media Manager',
            'dm' => 'Digital Marketing Specialist',
            'mktg' => 'Marketing Manager',
            'mr' => 'Market Research Analyst',
            'sales' => 'Sales Associate',
            'sm' => 'Sales Manager',
            'kam' => 'Key Account Manager',
            'procurement' => 'Procurement Specialist',
            'purchasing' => 'Purchasing Manager',
            'buyer' => 'Buyer',
            'sourcing' => 'Sourcing Analyst',
            'vendor' => 'Vendor Relations Coordinator'
        ];
        
        if (array_key_exists($position, $synonyms)) {
            return $synonyms[$position];
        }
        
        // Final fallback - return the original with basic capitalization
        return ucwords($position);
    }
    
    public function apiIndex()
    {
        $employees = Employee::all();
    
        return response()->json([
            'employees' => $employees
        ], 200);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employeeName = $employee->first_name . ' ' . $employee->last_name;
        $employee->delete();

        return redirect()->route('admin.employees.archived')
            ->with('success', 'Employee ' . $employeeName . ' deleted successfully.');
    }

    public function archived()
    {
        $employees = Employee::onlyTrashed()->paginate(10);
        return view('admin.employees.archived', compact('employees'));
    }

    public function restore($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->restore();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee ' . $employee->first_name . ' ' . $employee->last_name . ' restored successfully.');
    }

    public function dashboard()
    {
        $employee = auth()->user();
        $records = $employee->records;

        return view('employees.dashboard', compact('employee', 'records'));
    }

    public function updateStatus(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,reject'
        ]);
    
        $employee->update(['status' => $validated['status']]);
    
        $counts = [
            'total' => Employee::count(),
            'pending' => Employee::where('status', 'pending')->count(),
            'approved' => Employee::where('status', 'approved')->count(),
            'reject' => Employee::where('status', 'reject')->count(),
        ];
    
        return response()->json([
            'message' => 'Status updated successfully',
            'counts' => $counts
        ]);
    }

    public function export(Request $request)
{
    $status = $request->input('status');
    
    return Excel::download(new EmployeesExport($status), 'employees.xlsx');
}
}