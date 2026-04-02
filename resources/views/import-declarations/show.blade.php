@extends('layouts.app')

@section('title', 'Chi tiết Tờ khai Tạm nhập')

@section('content')
<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('import-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $importDeclaration->declaration_number }}</h2>
            @php
                $statusMap = [
                    'active'      => ['bg-green-100 text-green-800',  'Hiệu lực'],
                    'extended'    => ['bg-blue-100 text-blue-800',    'Gia hạn'],
                    're_exported' => ['bg-gray-100 text-gray-700',    'Đã tái xuất'],
                    'expired'     => ['bg-red-100 text-red-800',      'Hết hạn'],
                ];
                [$cls, $label] = $statusMap[$importDeclaration->status] ?? ['bg-gray-100 text-gray-700', $importDeclaration->status];
            @endphp
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $cls }}">{{ $label }}</span>
        </div>
        <a href="{{ route('import-declarations.edit', $importDeclaration) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Chỉnh sửa
        </a>
    </div>

    {{-- Basic info --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin tờ khai</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số tờ khai</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->declaration_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ngày đăng ký</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->registration_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Hạn tái xuất</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->expiry_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Chi cục hải quan</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->customs_office ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Hóa đơn</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->invoice_number ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin người nhập khẩu</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tên người nhập khẩu</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->importer_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mã người nhập khẩu</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->importer_code ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Người xuất khẩu</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->exporter_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nước xuất khẩu</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->exporter_country ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Trọng lượng</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->gross_weight ? $importDeclaration->gross_weight . ' ' . $importDeclaration->weight_unit : '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Items table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Danh sách mặt hàng ({{ $importDeclaration->items->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">STT</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Mã HS</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tên hàng hóa</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Model</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số lượng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Serial đã nhập</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($importDeclaration->items as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 text-gray-600 font-mono text-xs">{{ $item->hs_code ?? '—' }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->equipment_name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->model ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->serials->count() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-400">Chưa có mặt hàng nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Export declarations linked --}}
    @if($importDeclaration->exportDeclarations->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tờ khai tạm xuất liên quan ({{ $importDeclaration->exportDeclarations->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số tờ khai</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Khách hàng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Ngày xuất</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($importDeclaration->exportDeclarations as $export)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-blue-700">{{ $export->declaration_number }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $export->customer?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $export->registration_date?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $export->status }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('export-declarations.show', $export) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Xem</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection