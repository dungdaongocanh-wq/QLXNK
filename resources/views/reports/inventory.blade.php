@extends('layouts.app')

@section('title', 'Báo cáo Xuất nhập tồn')

@section('content')
<div class="py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Báo cáo Xuất nhập tồn</h2>

    {{-- Summary cards --}}
    @php
        $totalIn    = $items->sum('total_qty');
        $totalStock = $items->sum('in_stock');
        $totalRent  = $items->sum('rented_out');
        $totalRex   = $items->sum('re_exported');
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Tổng nhập</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalIn }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Trong kho</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalStock }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Đang cho thuê</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $totalRent }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Đã tái xuất</p>
            <p class="text-3xl font-bold text-gray-600 mt-1">{{ $totalRex }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Chi tiết theo mặt hàng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tên hàng hóa</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Model</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tờ khai</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600">Tổng nhập</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600">Trong kho</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600">Cho thuê</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600">Tái xuất</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item['equipment_name'] }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item['model'] ?? '—' }}</td>
                        <td class="px-5 py-3 text-blue-600 text-xs">{{ $item['declaration'] ?? '—' }}</td>
                        <td class="px-5 py-3 text-center text-gray-700 font-medium">{{ $item['total_qty'] }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">{{ $item['in_stock'] }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">{{ $item['rented_out'] }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ $item['re_exported'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Chưa có dữ liệu tồn kho
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($items->isNotEmpty())
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="3" class="px-5 py-3 font-semibold text-gray-700">Tổng cộng</td>
                        <td class="px-5 py-3 text-center font-bold text-gray-800">{{ $totalIn }}</td>
                        <td class="px-5 py-3 text-center font-bold text-green-700">{{ $totalStock }}</td>
                        <td class="px-5 py-3 text-center font-bold text-indigo-700">{{ $totalRent }}</td>
                        <td class="px-5 py-3 text-center font-bold text-gray-700">{{ $totalRex }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection