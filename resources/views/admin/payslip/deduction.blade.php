<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Employee Deduction</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="main-content min-h-screen">
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <div class="flex-grow p-16">
            <h1 class="text-3xl font-bold mb-10 -mt-10 text-left">Employee Deduction</h1>
            
            @if(session('error'))
                <div class="text-red-500 bg-red-100 p-3 rounded-md mb-4">{{ session('error') }}</div>
            @endif

            <div class="flex justify-end items-center mb-6"> 
                <form method="GET" action="#" class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" name="search" id="searchInput" value=""
                            class="border border-gray-300 rounded-lg px-4 py-2 w-72 focus:ring focus:ring-yellow-300"
                            placeholder="Search payslips...">
                        <button type="submit"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition">🔍</button>
                    </div>
                </form>
            </div>

              <!-- Table Layout Only -->
              <div class="overflow-x-auto bg-white">
                <table class="w-full border-collapse border border-gray-200">
                    <thead class="linear-gradient">
                        <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Employee Name</th>
            <th class="border px-4 py-2">Contact</th>
            <th class="border px-4 py-2">Employer</th>
            <th class="border px-4 py-2">Department</th>
            <th class="border px-4 py-2">Income Tax</th>
            <th class="border px-4 py-2">Pension</th>
            <th class="border px-4 py-2">Health Assurance</th>
            <th class="border px-4 py-2">Deductions</th>
            <th class="border px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payslips as $payslip)
        <tr>
            <td class="border px-4 py-2">{{ $payslip['employee_id'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['employee_name'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['contact'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['employer'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['department'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['income_tax'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['pension'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['health_insurance'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['total_deductions'] }}</td>
            <td class="border px-4 py-2">
                <button onclick="openPayslipModal(
                    '{{ $payslip['employee_id'] }}', 
                    '{{ $payslip['employee_name'] }}', 
                    '{{ $payslip['contact'] }}', 
                    '{{ $payslip['employer'] }}', 
                    '{{ $payslip['department'] }}',  
                    '{{ $payslip['income_tax'] }}', 
                    '{{ $payslip['pension'] }}', 
                    '{{ $payslip['health_insurance'] }}', 
                    '{{ $payslip['total_deductions'] }}', 
                )" class="text-blue-500 hover:underline">
                    View
                </button>
                |
                <a href="{{ route('download.payslip', [
    'employee_name' => $payslip['employee_name'],
    'pay_period' => $payslip['pay_period'],
    'payment_date' => $payslip['payment_date'],
    'base_salary' => $payslip['base_salary'],
    'overtime' => $payslip['overtime'],
    'bonuses' => $payslip['bonuses'],
    'gross_earnings' => $payslip['gross_earnings'],
    'total_deductions' => $payslip['total_deductions'],
    'net_pay' => $payslip['net_pay']
]) }}" class="text-green-500 hover:underline">Download</a>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

            </div>
        </div>
    </div>
    <div id="payslipModal" class="hidden fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-6xl overflow-x-auto">
        <h2 class="text-lg font-bold text-center mb-4">Payslip Details</h2>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-yellow-400">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Employee Name</th>
                        <th class="border px-4 py-2">Contact</th>
                        <th class="border px-4 py-2">Employer</th>
                        <th class="border px-4 py-2">Department</th>
                        <th class="border px-4 py-2">Income Tax</th>
                        <th class="border px-4 py-2">Pension</th>
                        <th class="border px-4 py-2">Health Assurance</th>
                        <th class="border px-4 py-2">Deductions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center font-semibold bg-white">
                        <td class="border px-4 py-2" id="modalEmployeeID"></td>
                        <td class="border px-4 py-2" id="modalEmployeeName"></td>
                        <td class="border px-4 py-2" id="modalContact"></td>
                        <td class="border px-4 py-2" id="modalEmployer"></td>
                        <td class="border px-4 py-2" id="modalDepartment"></td>
                        <td class="border px-4 py-2" id="modalIncomeTax"></td>
                        <td class="border px-4 py-2" id="modalPension"></td>
                        <td class="border px-4 py-2" id="modalHealthAssurance"></td>
                        <td class="border px-4 py-2" id="modalDeductions"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-4">
            <button onclick="closePayslipModal()" class="bg-red-500 text-white px-6 py-2 rounded-lg shadow hover:bg-red-600">
                Close
            </button>
        </div>
    </div>
</div>


<script>
    function openPayslipModal(id, name, contact, employer, department, tax, pension, health, deductions) {
    document.getElementById('modalEmployeeID').textContent = id;
    document.getElementById('modalEmployeeName').textContent = name;
    document.getElementById('modalContact').textContent = contact;
    document.getElementById('modalEmployer').textContent = employer;
    document.getElementById('modalDepartment').textContent = department;
    document.getElementById('modalIncomeTax').textContent = tax;
    document.getElementById('modalPension').textContent = pension;
    document.getElementById('modalHealthAssurance').textContent = health;
    document.getElementById('modalDeductions').textContent = deductions;

    document.getElementById('payslipModal').classList.remove('hidden');
}

function closePayslipModal() {
    document.getElementById('payslipModal').classList.add('hidden');
}



    </script>
</body>
</html>
