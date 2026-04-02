@extends('layouts.app')

@section('title', 'Chi tiết Tờ khai Tạm xuất')

@section('content')
<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('export-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $exportDeclaration->declaration_number }}</h2>
            @php
                $statusMap = [
                    'active'             => ['bg-green-100 text-green-800',   'Hiệu lực'],
                    'partially_returned' => ['bg-yellow-100 text-yellow-800', 'Trả một phần'],
                    'fully_returned'     => ['bg-gray-100 text-gray-700',     'Đã hoàn trả'],
                    'overdue'            => ['bg-red-100 text-red-800',        'Quá hạn'],
                ];
                [$cls, $label] = $statusMap[$exportDeclaration->status] ?? ['bg-gray-100 text-gray-700', $exportDeclaration->status];
            @endphp
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $cls }}">{{ $label }}</span>
        </div>
        <a href="{{ route('export-declarations.edit', $exportDeclaration) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Chỉnh sửa
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin tờ khai</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số tờ khai</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->declaration_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ngày đăng ký</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->registration_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Hạn tái nhập</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->expiry_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Chi cục hải quan</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->customs_office ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tờ khai tạm nhập gốc</dt>
                    <dd class="font-medium text-blue-700">
                        @if($exportDeclaration->importDeclaration)
                            <a href="{{ route('import-declarations.show', $exportDeclaration->importDeclaration) }}" class="hover:underline">
                                {{ $exportDeclaration->importDeclaration->declaration_number }}
                            </a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin khách hàng</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Khách hàng</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->customer?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Hóa đơn</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->invoice_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tổng giá trị</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->total_value ? number_format($exportDeclaration->total_value, 2) . ' ' . $exportDeclaration->currency : '—' }}</dd>
                </div>
                @if($exportDeclaration->notes)
                <div>
                    <dt class="text-gray-500 mb-1">Ghi chú</dt>
                    <dd class="text-gray-700 bg-gray-50 rounded p-2">{{ $exportDeclaration->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Serial items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Danh sách Serial ({{ $exportDeclaration->serialItems->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Serial Number</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tên thiết bị</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($exportDeclaration->serialItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-gray-800">{{ $item->serial?->serial_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->serial?->importItem?->equipment_name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $item->serial?->status ?? '—' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-gray-400">Chưa có serial nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Reimport records --}}
    @if($exportDeclaration->reimportRecords->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Lịch sử tái nhập ({{ $exportDeclaration->reimportRecords->count() }})</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($exportDeclaration->reimportRecords as $reimport)
            <div class="px-5 py-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-800">Tái nhập ngày {{ $reimport->created_at->format('d/m/Y') }}</span>
                    <span class="text-gray-500">{{ $reimport->serialItems->count() }} serial</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection