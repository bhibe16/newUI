<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('logout') }}">
    <title>HRIS - Employee Payslips</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="main-content min-h-screen">
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <div class="flex-grow p-16">
            <h1 class="text-3xl font-bold mb-10 -mt-10 text-left">Employee Bonuses</h1>
            
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
            <th class="border px-4 py-2">Pay Period</th>
            <th class="border px-4 py-2">Payment Date</th>
            <th class="border px-4 py-2">Bonuses</th>
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
            <td class="border px-4 py-2">{{ $payslip['pay_period'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['payment_date'] }}</td>
            <td class="border px-4 py-2">{{ $payslip['bonuses'] }}</td>
            <td class="border px-4 py-2">
                <button onclick="openPayslipModal(
                    '{{ $payslip['employee_id'] }}', 
                    '{{ $payslip['employee_name'] }}', 
                    '{{ $payslip['contact'] }}', 
                    '{{ $payslip['employer'] }}', 
                    '{{ $payslip['department'] }}', 
                    '{{ $payslip['pay_period'] }}', 
                    '{{ $payslip['payment_date'] }}', 
                    '{{ $payslip['bonuses'] }}'
                )" class="text-blue-500 hover:underline">
                    View
                </button>
                |
                <a href="{{ route('download.payslip', [
    'employee_id' => $payslip['employee_id'],               
    'employee_name' => $payslip['employee_name'],
    'employee_id' => $payslip['contact'],
    'employee_id' => $payslip['employer'],
    'employee_id' => $payslip['department'],
    'pay_period' => $payslip['pay_period'],
    'payment_date' => $payslip['payment_date'],
    'bonuses' => $payslip['bonuses']
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
    <div class="bg-white p-4 rounded-lg shadow-lg w-auto max-w-4xl">
        <h2 class="text-lg font-bold text-center mb-4">Payslip Details</h2>

        <!-- Table Header (One Row) -->
        <table class="w-full border-collapse border border-gray-300 text-sm">
        <thead class="linear-gradient">
                <tr>
                <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Employee Name</th>
            <th class="border px-4 py-2">Contact</th>
            <th class="border px-4 py-2">Employer</th>
            <th class="border px-4 py-2">Department</th>
            <th class="border px-4 py-2">Pay Period</th>
            <th class="border px-4 py-2">Payment Date</th>
            <th class="border px-4 py-2">Bonuses</th>
                </tr>
            </thead>
            <tbody>
    <tr class="text-center font-semibold">
        <td class="border px-2 py-1" id="modalEmployeeID"></td>
        <td class="border px-2 py-1" id="modalEmployeeName"></td>
        <td class="border px-2 py-1" id="modalContact"></td>
        <td class="border px-2 py-1" id="modalEmployer"></td>
        <td class="border px-2 py-1" id="modalDepartment"></td>
        <td class="border px-2 py-1" id="modalPayPeriod"></td>
        <td class="border px-2 py-1" id="modalPaymentDate"></td>
        <td class="border px-2 py-1" id="modalBonuses"></td>
    </tr>
</tbody>

        </table>

        <div class="flex justify-end mt-4">
            <button onclick="closePayslipModal()" class="bg-red-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
</div>

<script>
    function openPayslipModal(id, name, contact, employer, department, period, date, bonuses) {
    document.getElementById('modalEmployeeID').textContent = id;
    document.getElementById('modalEmployeeName').textContent = name;
    document.getElementById('modalContact').textContent = contact;
    document.getElementById('modalEmployer').textContent = employer;
    document.getElementById('modalDepartment').textContent = department;
    document.getElementById('modalPayPeriod').textContent = period;
    document.getElementById('modalPaymentDate').textContent = date;
    document.getElementById('modalBonuses').textContent = bonuses;

    document.getElementById('payslipModal').classList.remove('hidden');
}

function closePayslipModal() {
    document.getElementById('payslipModal').classList.add('hidden');
}



    </script>
</body>
</html>
