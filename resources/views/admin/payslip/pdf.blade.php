<!DOCTYPE html>
<html>
<head>
    <title>Payslip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo Section -->
        <div class="logo">
        <img src="{{ public_path('images/logo.png') }}" alt="Company Logo">
        </div>

        <!-- Payslip Heading -->
        <h2>Payslip for {{ $payslipData['employee_name'] ?? 'N/A' }}</h2>

        <!-- Payslip Details Table -->
        <table>
            <tr><th>Employee ID</th><td>{{ $payslipData['employee_id'] ?? 'N/A' }}</td></tr>
            <tr><th>Employee Name</th><td>{{ $payslipData['employee_name'] ?? 'N/A' }}</td></tr>
            <tr><th>Contact</th><td>{{ $payslipData['contact'] ?? 'N/A' }}</td></tr>
            <tr><th>Employer</th><td>{{ $payslipData['employer'] ?? 'N/A' }}</td></tr>
            <tr><th>Department</th><td>{{ $payslipData['department'] ?? 'N/A' }}</td></tr>
            <tr><th>Pay Period</th><td>{{ $payslipData['pay_period'] ?? 'N/A' }}</td></tr>
            <tr><th>Payment Date</th><td>{{ $payslipData['payment_date'] ?? 'N/A' }}</td></tr>
            <tr><th>Base Salary</th><td>{{ $payslipData['base_salary'] ?? 'N/A' }}</td></tr>
            <tr><th>Overtime</th><td>{{ $payslipData['overtime'] ?? 'N/A' }}</td></tr>
            <tr><th>Gross Earnings</th><td>{{ $payslipData['gross_earnings'] ?? 'N/A' }}</td></tr>
            <tr><th>Net Pay</th><td>{{ $payslipData['net_pay'] ?? 'N/A' }}</td></tr>
            <tr><th>Social Security</th><td>{{ $payslipData['social_security'] ?? 'N/A' }}</td></tr>
            <tr><th>Total Earnings</th><td>{{ $payslipData['total_earnings'] ?? 'N/A' }}</td></tr>
        </table>
    </div>
</body>
</html>