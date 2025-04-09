<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Employee Payslips</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .payslip-card {
            transition: all 0.3s ease;
        }
        .payslip-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .summary-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 10px;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        @media (max-width: 768px) {
            .table-header {
                display: none;
            }
            .mobile-card-view {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <div class="flex-grow p-4 md:p-8">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Employee Payslips</h1>
                    <p class="text-gray-600">View and manage employee compensation records</p>
                </div>
                
                <!-- Search and Filters -->
                <div class="mt-4 md:mt-0 w-full md:w-auto">
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="relative flex-grow">
                            <input type="text" name="search" id="searchInput" value=""
                                class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400"
                                placeholder="Search employee or ID...">
                            <button type="submit"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-yellow-500 text-white p-1 rounded-md hover:bg-yellow-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                        <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-300">
                            <option>All Departments</option>
                            <option>Finance</option>
                            <option>HR</option>
                            <option>IT</option>
                        </select>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="summary-card p-4 shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium">Total Payslips</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ count($payslips) }}</p>
                </div>
                <div class="summary-card p-4 shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium">This Month's Payroll</h3>
                    <p class="text-2xl font-bold text-green-600">${{ number_format(array_sum(array_column($payslips, 'total_earnings')), 2) }}</p>
                </div>
                <div class="summary-card p-4 shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium">Average Net Pay</h3>
                    <p class="text-2xl font-bold text-blue-600">${{ number_format(array_sum(array_column($payslips, 'net_pay')) / count($payslips), 2) }}</p>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Period</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($payslips as $payslip)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600 font-medium">{{ substr($payslip['employee_name'], 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $payslip['employee_name'] }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $payslip['employee_id'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payslip['department'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payslip['pay_period'] }}</div>
                                <div class="text-sm text-gray-500">{{ $payslip['payment_date'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($payslip['gross_earnings'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                ${{ number_format($payslip['net_pay'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openPayslipModal(
                                    '{{ $payslip['employee_id'] }}', 
                                    '{{ $payslip['employee_name'] }}', 
                                    '{{ $payslip['contact'] }}', 
                                    '{{ $payslip['employer'] }}', 
                                    '{{ $payslip['department'] }}', 
                                    '{{ $payslip['pay_period'] }}', 
                                    '{{ $payslip['payment_date'] }}', 
                                    '{{ $payslip['base_salary'] }}', 
                                    '{{ $payslip['overtime'] }}',  
                                    '{{ $payslip['gross_earnings'] }}',  
                                    '{{ $payslip['net_pay'] }}',
                                    '{{ $payslip['social_security'] }}', 
                                    '{{ $payslip['total_earnings'] }}'
                                )" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    View
                                </button>
                                <a href="#" class="text-blue-600 hover:text-blue-900">PDF</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                @foreach ($payslips as $payslip)
                <div class="payslip-card bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $payslip['employee_name'] }}</h3>
                            <p class="text-sm text-gray-500">ID: {{ $payslip['employee_id'] }}</p>
                        </div>
                        <span class="badge bg-green-100 text-green-800 rounded-full">Paid</span>
                    </div>
                    
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-xs text-gray-500">Department</p>
                            <p class="text-sm font-medium">{{ $payslip['department'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Pay Period</p>
                            <p class="text-sm font-medium">{{ $payslip['pay_period'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Gross Pay</p>
                            <p class="text-sm font-medium">${{ number_format($payslip['gross_earnings'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Net Pay</p>
                            <p class="text-sm font-medium text-green-600">${{ number_format($payslip['net_pay'], 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end space-x-2">
                        <button onclick="openPayslipModal(
                            '{{ $payslip['employee_id'] }}', 
                            '{{ $payslip['employee_name'] }}', 
                            '{{ $payslip['contact'] }}', 
                            '{{ $payslip['employer'] }}', 
                            '{{ $payslip['department'] }}', 
                            '{{ $payslip['pay_period'] }}', 
                            '{{ $payslip['payment_date'] }}', 
                            '{{ $payslip['base_salary'] }}', 
                            '{{ $payslip['overtime'] }}',  
                            '{{ $payslip['gross_earnings'] }}',  
                            '{{ $payslip['net_pay'] }}',
                            '{{ $payslip['social_security'] }}', 
                            '{{ $payslip['total_earnings'] }}'
                        )" class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">
                            Details
                        </button>
                        <button class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                            Download
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">{{ count($payslips) }}</span> results
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border rounded text-sm bg-white text-gray-700 hover:bg-gray-50">
                        Previous
                    </button>
                    <button class="px-3 py-1 border rounded text-sm bg-white text-gray-700 hover:bg-gray-50">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payslip Modal -->
    <div id="payslipModal" class="hidden fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50 z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Payslip Details</h2>
                <button onclick="closePayslipModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="overflow-y-auto p-6">
                <!-- Employee Info -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Employee</h3>
                        <p class="mt-1 text-lg font-semibold" id="modalEmployeeName"></p>
                        <p class="text-sm text-gray-500">ID: <span id="modalEmployeeID"></span></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Department</h3>
                        <p class="mt-1 text-lg font-semibold" id="modalDepartment"></p>
                        <p class="text-sm text-gray-500">Employer: <span id="modalEmployer"></span></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Pay Period</h3>
                        <p class="mt-1 text-lg font-semibold" id="modalPayPeriod"></p>
                        <p class="text-sm text-gray-500">Payment Date: <span id="modalPaymentDate"></span></p>
                    </div>
                </div>

                <!-- Earnings Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Earnings</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Base Salary</p>
                                <p class="text-lg font-semibold" id="modalBaseSalary"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Overtime</p>
                                <p class="text-lg font-semibold" id="modalOvertime"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Gross Earnings</p>
                                <p class="text-lg font-semibold text-green-600" id="modalGrossEarnings"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deductions Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Deductions</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Social Security</p>
                                <p class="text-lg font-semibold text-red-600" id="modalSocialSecurity"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tax Withholding</p>
                                <p class="text-lg font-semibold text-red-600">$0.00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-blue-800">Net Pay</h3>
                        <p class="text-2xl font-bold text-blue-600" id="modalNetPay"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Buttons at the Bottom) -->
            <div class="px-6 py-4 border-t flex justify-end space-x-3">
                <a id="downloadPayslip" href="#" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </a>
                <button onclick="closePayslipModal()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function openPayslipModal(id, name, contact, employer, department, period, date, salary, overtime, earnings, netPay, security, total) {
            // Format currency values
            const formatCurrency = (value) => {
                return '$' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            };

            document.getElementById('modalEmployeeID').textContent = id;
            document.getElementById('modalEmployeeName').textContent = name;
            document.getElementById('modalContact').textContent = contact;
            document.getElementById('modalEmployer').textContent = employer;
            document.getElementById('modalDepartment').textContent = department;
            document.getElementById('modalPayPeriod').textContent = period;
            document.getElementById('modalPaymentDate').textContent = date;
            document.getElementById('modalBaseSalary').textContent = formatCurrency(salary);
            document.getElementById('modalOvertime').textContent = formatCurrency(overtime);
            document.getElementById('modalGrossEarnings').textContent = formatCurrency(earnings);
            document.getElementById('modalNetPay').textContent = formatCurrency(netPay);
            document.getElementById('modalSocialSecurity').textContent = formatCurrency(security);
            document.getElementById('modalTotalEarnings').textContent = formatCurrency(total);
            
            // Update download link dynamically
            document.getElementById('downloadPayslip').href = `{{ route('download.payslip') }}?employee_name=${name}&pay_period=${period}&payment_date=${date}&base_salary=${salary}&overtime=${overtime}&gross_earnings=${earnings}&net_pay=${netPay}&social_security=${security}&total_earnings=${total}`;
            
            document.getElementById('payslipModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closePayslipModal() {
            document.getElementById('payslipModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('payslipModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePayslipModal();
            }
        });
    </script>
</body>
</html>