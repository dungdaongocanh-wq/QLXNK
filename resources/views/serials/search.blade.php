@extends('layouts.app')

@section('title', 'Tra cứu Serial')

@section('content')
<div class="py-6 max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tra cứu Serial Number</h2>

    {{-- Search form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form action="{{ route('serials.search') }}" method="GET" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="q" value="{{ $query }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Nhập serial number để tìm kiếm..."
                       autofocus>
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Tìm kiếm
            </button>
        </form>
    </div>

    {{-- Results --}}
    @if($query)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Kết quả tìm kiếm</h3>
            <span class="text-sm text-gray-500">{{ count($serials) }} kết quả cho "{{ $query }}"</span>
        </div>

        @if($serials->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Serial Number</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Thiết bị</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tờ khai tạm nhập</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tình trạng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Khách hàng hiện tại</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($serials as $serial)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono font-semibold text-gray-800">{{ $serial->serial_number }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $serial->importItem?->equipment_name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($serial->importItem?->importDeclaration)
                            <a href="{{ route('import-declarations.show', $serial->importItem->importDeclaration) }}"
                               class="text-blue-600 hover:underline text-xs">
                                {{ $serial->importItem->importDeclaration->declaration_number }}
                            </a>
                            @else
                            —
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $statusMap = [
                                    'in_stock'    => ['bg-green-100 text-green-800',  'Trong kho'],
                                    'rented_out'  => ['bg-blue-100 text-blue-800',    'Đang cho thuê'],
                                    're_exported' => ['bg-gray-100 text-gray-700',    'Đã tái xuất'],
                                ];
                                [$cls, $label] = $statusMap[$serial->status] ?? ['bg-gray-100 text-gray-700', $serial->status];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $cls }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $serial->currentExport?->customer?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('serials.history', $serial) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Lịch sử</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-5 py-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Không tìm thấy serial nào khớp với "{{ $query }}"
        </div>
        @endif
    </div>
    @endif
</div>
@endsection