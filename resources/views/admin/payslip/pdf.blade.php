<!DOCTYPE html>
<html>
<head>
    <title>Payslip</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
    </style>
</head>
<body>
<h2>Payslip for {{ $payslipData['employee_name'] ?? 'N/A' }}</h2>

<table>
    <tr><th>Pay Period</th><td>{{ $payslipData['pay_period'] ?? 'N/A' }}</td></tr>
    <tr><th>Payment Date</th><td>{{ $payslipData['payment_date'] ?? 'N/A' }}</td></tr>
    <tr><th>Base Salary</th><td>{{ $payslipData['base_salary'] ?? 'N/A' }}</td></tr>
    <tr><th>Overtime</th><td>{{ $payslipData['overtime'] ?? 'N/A' }}</td></tr>
    <tr><th>Bonuses</th><td>{{ $payslipData['bonuses'] ?? 'N/A' }}</td></tr>
    <tr><th>Gross Earnings</th><td>{{ $payslipData['gross_earnings'] ?? 'N/A' }}</td></tr>
    <tr><th>Deductions</th><td>{{ $payslipData['total_deductions'] ?? 'N/A' }}</td></tr>
    <tr><th>Net Pay</th><td>{{ $payslipData['net_pay'] ?? 'N/A' }}</td></tr>
</table>
</body>
</html>
