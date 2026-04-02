@extends('layouts.app')

@section('title', 'Báo cáo Doanh thu Cho thuê')

@section('content')
<div class="py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Báo cáo Doanh thu Cho thuê</h2>

    {{-- Filter form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <form action="{{ route('reports.rental-revenue') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Lọc
            </button>
        </form>
    </div>

    {{-- Summary --}}
    @php
        $totalDeclarations = $exports->count();
        $totalSerials      = $exports->sum(fn($e) => $e->serialItems->count());
    @endphp
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Tổng tờ khai</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalDeclarations }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Tổng serial cho thuê</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $totalSerials }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Chi tiết tờ khai tạm xuất ({{ $from }} → {{ $to }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số tờ khai</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Khách hàng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Ngày xuất</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Hạn tái nhập</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600">Số serial</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($exports as $export)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-blue-700">
                            <a href="{{ route('export-declarations.show', $export) }}" class="hover:underline">
                                {{ $export->declaration_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $export->customer?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $export->registration_date?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $export->expiry_date?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-center text-gray-700 font-medium">{{ $export->serialItems->count() }}</td>
                        <td class="px-5 py-3">
                            @php
                                $statusMap = [
                                    'active'             => ['bg-green-100 text-green-800',   'Hiệu lực'],
                                    'partially_returned' => ['bg-yellow-100 text-yellow-800', 'Trả một phần'],
                                    'fully_returned'     => ['bg-gray-100 text-gray-700',     'Đã hoàn trả'],
                                    'overdue'            => ['bg-red-100 text-red-800',        'Quá hạn'],
                                ];
                                [$cls, $label] = $statusMap[$export->status] ?? ['bg-gray-100 text-gray-700', $export->status];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $cls }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('export-declarations.show', $export) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Xem</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Không có dữ liệu trong khoảng thời gian này
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection