<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmploymentHistory;
use App\Models\EducationalHistory;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
{
    $query = $request->input('search');
    $status = $request->input('status');

    // Initialize the employee query
    $employeeQuery = Employee::query()->with(['department', 'position']);

    // Check for status in search query if not provided as parameter
    $validStatuses = ['approved', 'reject', 'pending'];
    if (!$status && $query) {
        foreach ($validStatuses as $validStatus) {
            if (strtolower($query) === $validStatus) {
                $status = $validStatus;
                $query = null; // Clear the query since we're using it for status
                break;
            }
        }
    }

    // Apply status filter if provided (either from input or detected from search)
    if ($status && in_array($status, $validStatuses)) {
        $employeeQuery->where('status', $status);
    }

    // Check if query contains natural language patterns
    $nlpFilters = $this->parseNaturalLanguageQuery($query);
    
    // Apply NLP filters if detected
    if ($nlpFilters) {
        if (isset($nlpFilters['department'])) {
            // Special case for HR - use exact match immediately
            if ($nlpFilters['department'] === 'Human Resources') {
                $department = Department::where('name', 'Human Resources')->first();
            } else {
                $normalized = $this->normalizeDepartment($nlpFilters['department']);
                $department = Department::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($normalized))])
                              ->first();
                
                if (!$department) {
                    // Fallback for IT department
                    if (strpos(strtolower($normalized), 'it') !== false || 
                        strpos(strtolower($normalized), 'information technology') !== false) {
                        $department = Department::where('name', 'IT (Information Technology)')->first();
                    }
                    // Fallback for other departments
                    else {
                        $department = Department::whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($normalized).'%'])
                                      ->first();
                    }
                }
            }
            
            if ($department) {
                $employeeQuery->where('department_id', $department->id);
            }
        }
        
        if (isset($nlpFilters['position'])) {
            $normalizedPosition = $this->normalizePosition($nlpFilters['position']);
            
            // Handle array results (for grouped positions like all managers)
            if (is_array($normalizedPosition)) {
                $employeeQuery->whereHas('position', function($q) use ($normalizedPosition) {
                    $q->whereIn('name', $normalizedPosition);
                });
            } 
            // Handle single position results
            else {
                $position = Position::where('name', $normalizedPosition)->first();
                if ($position) {
                    $employeeQuery->where('position_id', $position->id);
                }
            }
        }
    }
    // Apply regular search if no NLP filters and query exists
    elseif ($query) {
        $employeeQuery->where(function($q) use ($query) {
            $q->where('first_name', 'like', "%$query%")
              ->orWhere('last_name', 'like', "%$query%")
              ->orWhere('user_id', 'like', "%$query%")
              ->orWhere('email', 'like', "%$query%")
              ->orWhereHas('department', function($q) use ($query) {
                  $q->where('name', 'like', "%$query%");
              })
              ->orWhereHas('position', function($q) use ($query) {
                  $q->where('name', 'like', "%$query%");
              });
        });
    }

    // Get paginated results
    $employees = $employeeQuery->paginate(10);

    // Fetch related employment and educational data
    $employment = EmploymentHistory::whereIn('user_id', $employees->pluck('user_id'))->get()->groupBy('user_id');
    $educational = EducationalHistory::whereIn('user_id', $employees->pluck('user_id'))->get()->groupBy('user_id');
    
    return view('admin.employees.index', compact('employees', 'employment', 'educational'));
}

private function parseNaturalLanguageQuery($query)
{
    if (empty($query)) return null;

    $query = strtolower($query);
    $filters = [];

    // First check for HR-specific patterns
    if (preg_match('/\b(hr|human\s?resources?)\b/i', $query, $matches)) {
        $filters['department'] = 'Human Resources';
    }
    else {
        // Regular department patterns
        $departmentPatterns = [
            '/\b(all|show|list|find)\s+(employees|staff)\s+(in|from|of)\s+(.+?)\s*(department|dept|team)\b/i' => 4,
            '/\b(employees|staff)\s+(in|from|of)\s+(.+?)\s*(department|dept|team)\b/i' => 3,
            '/\b(.+?)\s+(department|dept|team)\s+(employees|staff)\b/i' => 1,
        ];
        
        foreach ($departmentPatterns as $pattern => $group) {
            if (preg_match($pattern, $query, $matches)) {
                $department = trim($matches[$group]);
                if (strlen($department) > 3 && !in_array($department, ['staff','employees','team'])) {
                    $filters['department'] = $this->normalizeDepartment($department);
                    break;
                }
            }
        }
    }

    // Enhanced position patterns
    $positionPatterns = [
        '/\b(all|show|list|find|search)\s+(.+?)\s+(positions?|roles?|people|staff|members)\b/i' => 2,
        '/\b(employees|staff|people)\s+(with|who\s+are|working\s+as)\s+(.+?)\s*(positions?|roles?)?\b/i' => 3,
        '/\b(.+?)\s+(positions?|roles?|jobs?)\b/i' => 1,
        '/\b(looking\s+for|searching\s+for|find)\s+(.+?)\s*(positions?|roles?)?\b/i' => 2,
        '/\b(need|want)\s+(.+?)\s*(positions?|roles?)?\b/i' => 2,
    ];

    foreach ($positionPatterns as $pattern => $group) {
        if (preg_match($pattern, $query, $matches)) {
            $position = trim($matches[$group]);
            if (strlen($position) > 2 && !in_array($position, ['staff','employees','team'])) {
                $filters['position'] = $position;
                break;
            }
        }
    }

    // Fallback: if the entire query looks like a position name
    if (empty($filters) && preg_match('/^[a-z\s&]+$/', $query)) {
        $filters['position'] = $query;
    }

    return empty($filters) ? null : $filters;
}

private function normalizeDepartment($department)
{
    $department = strtolower(trim($department));
    
    // Remove common suffixes only if they appear at the end
    $department = preg_replace('/(\s*(departments?|depts?|teams?|staff|employees?|members?)\s*)$/i', '', $department);
    
    // Special handling for IT department
    if (preg_match('/^(it|information\s?technology)$/', $department)) {
        return 'IT (Information Technology)';
    }
    
    // Other department mappings
    $mappings = [
        'finance' => 'Finance',
        'marketing' => 'Marketing',
        'purchasing' => 'Purchasing',
        'sales' => 'Sales',
        'merchandising' => 'Merchandising',
        'inventory management' => 'Inventory Management',
        'customer service' => 'Customer Service',
        'logistics' => 'Logistics'
    ];
    
    foreach ($mappings as $key => $value) {
        if ($department === $key) {
            return $value;
        }
    }
    
    return ucwords($department);
}

private function normalizePosition($position)
{
    $position = strtolower(trim($position));
    
    // Remove common suffixes
    $position = preg_replace('/\s*(positions?|roles?|jobs?|staff|employees?|titles?|people|members?)\s*$/i', '', $position);
    
    // Exact position mappings (all your positions)
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
    
    // Enhanced partial matching with word boundaries
    $partialMappings = [
        // Manager roles
        '/\bmanager\b/' => [
            'Purchasing Manager', 'Sales Manager', 'Marketing Manager',
            'Merchandising Manager', 'Brand Manager', 'Product Manager',
            'Category Manager', 'Inventory Control Manager', 'Finance Manager',
            'HR Manager', 'Customer Service Manager', 'IT Manager',
            'Logistics Manager', 'Fleet Manager'
        ],
        
        // Analyst roles
        '/\banalyst\b/' => [
            'Sourcing Analyst', 'Market Research Analyst', 'Financial Analyst',
            'Supply Chain Analyst', 'Compensation & Benefits Analyst',
            'Cybersecurity Analyst', 'Transportation Analyst', 'Stock Analyst'
        ],
        
        // Specialist roles
        '/\bspecialist\b/' => [
            'Procurement Specialist', 'Digital Marketing Specialist',
            'Accounts Payable/Receivable Specialist', 'Payroll Specialist',
            'Talent Acquisition Specialist', 'HR Generalist',
            'Customer Support Specialist', 'IT Support Specialist'
        ],
        
        // Coordinator roles
        '/\bcoordinator\b/' => [
            'Vendor Relations Coordinator', 'Warehouse Coordinator',
            'Client Relations Coordinator', 'Supply Chain Coordinator'
        ],
        
        // Representative roles
        '/\brepresentative\b/' => [
            'Business Development Representative', 'Call Center Representative',
            'Technical Support Representative'
        ],
        
        // Developer roles
        '/\bdeveloper\b/' => [
            'Software Developer'
        ],
        
        // Administrator roles
        '/\badministrator\b/' => [
            'Network Administrator'
        ],
        
        // Specific term mappings
        '/\bdigital\s*marketing\b/' => 'Digital Marketing Specialist',
        '/\bbrand\b/' => 'Brand Manager',
        '/\bsocial\s*media\b/' => 'Social Media Manager',
        '/\bmarket\s*research\b/' => 'Market Research Analyst',
        '/\bpurchasing\b/' => 'Purchasing Manager',
        '/\bprocurement\b/' => 'Procurement Specialist',
        '/\baccountant\b/' => 'Accountant',
        '/\bfinanc(e|ial)\b/' => 'Financial Analyst',
        '/\baccounts\s*payable\b/' => 'Accounts Payable/Receivable Specialist',
        '/\bpayroll\b/' => 'Payroll Specialist',
        '/\bhr\b/' => 'HR Manager',
        '/\btalent\s*acquisition\b/' => 'Talent Acquisition Specialist',
        '/\btraining\s*&\s*development\b/' => 'Training & Development Officer',
        '/\bcompensation\b/' => 'Compensation & Benefits Analyst',
        '/\bcustomer\s*service\b/' => 'Customer Service Manager',
        '/\bcall\s*center\b/' => 'Call Center Representative',
        '/\btechnical\s*support\b/' => 'Technical Support Representative',
        '/\bit\b/' => 'IT Manager',
        '/\bsoftware\b/' => 'Software Developer',
        '/\bnetwork\b/' => 'Network Administrator',
        '/\bcyber\s*security\b/' => 'Cybersecurity Analyst',
        '/\blogistics\b/' => 'Logistics Manager',
        '/\bsupply\s*chain\b/' => 'Supply Chain Analyst',
        '/\bwarehouse\b/' => 'Warehouse Coordinator',
        '/\bfleet\b/' => 'Fleet Manager',
        '/\bmarketing\b/' => 'Marketing Manager'
    ];
    
    foreach ($partialMappings as $pattern => $matches) {
        if (preg_match($pattern, $position)) {
            if (is_array($matches)) {
                return $matches; // Return array for grouped positions
            }
            return $matches;
        }
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
    $employeeName = $employee->first_name . ' ' . $employee->last_name; // Store name before deleting
    $employee->delete(); // Soft delete

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
        // Fetch employee-specific data
        $employee = auth()->user(); // Get the authenticated employee
        $records = $employee->records; // Assuming you have a relationship defined

        return view('employees.dashboard', compact('employees', 'records'));
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:approved,pending,reject'
    ]);

    $employee = Employee::findOrFail($id);
    $employee->status = $request->status;
    $employee->save();

    return response()->json(['success' => true]);
}


}
