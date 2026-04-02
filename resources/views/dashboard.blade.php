<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QLXNK - Dashboard</title>
</head>
<body>
<h1>Hệ thống Quản lý Xuất Nhập Khẩu</h1>

<h2>Tờ khai Tạm nhập</h2>
<ul>
    <li>Tổng số: {{ $importStats['total'] }}</li>
    <li>Đang hiệu lực: {{ $importStats['active'] }}</li>
    <li>Sắp hết hạn (30 ngày): {{ $importStats['expiring_30d'] }}</li>
    <li>Sắp hết hạn (7 ngày): {{ $importStats['expiring_7d'] }}</li>
</ul>

<h2>Tờ khai Tạm xuất</h2>
<ul>
    <li>Tổng số: {{ $exportStats['total'] }}</li>
    <li>Đang hiệu lực: {{ $exportStats['active'] }}</li>
    <li>Sắp hết hạn (30 ngày): {{ $exportStats['expiring_30d'] }}</li>
    <li>Quá hạn: {{ $exportStats['overdue'] }}</li>
</ul>

@if($importAlerts->isNotEmpty())
<h2>⚠️ Cảnh báo Tờ khai Tạm nhập sắp hết hạn</h2>
<table border="1">
    <tr><th>Số tờ khai</th><th>Ngày hết hạn</th><th>Trạng thái</th></tr>
    @foreach($importAlerts as $alert)
    <tr>
        <td>{{ $alert->declaration_number }}</td>
        <td>{{ $alert->expiry_date->format('d/m/Y') }}</td>
        <td>{{ $alert->status }}</td>
    </tr>
    @endforeach
</table>
@endif
</body>
</html>
