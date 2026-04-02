@extends('layouts.app')

@section('title', 'Chi tiết Tờ khai Xuất trả')

@section('content')
<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('reexport-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $reexportDeclaration->declaration_number }}</h2>
        </div>
        <a href="{{ route('reexport-declarations.edit', $reexportDeclaration) }}"
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
                    <dd class="font-medium text-gray-800">{{ $reexportDeclaration->declaration_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ngày đăng ký</dt>
                    <dd class="font-medium text-gray-800">{{ $reexportDeclaration->registration_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tờ khai tạm nhập gốc</dt>
                    <dd class="font-medium text-blue-700">
                        @if($reexportDeclaration->importDeclaration)
                            <a href="{{ route('import-declarations.show', $reexportDeclaration->importDeclaration) }}" class="hover:underline">
                                {{ $reexportDeclaration->importDeclaration->declaration_number }}
                            </a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Người tạo</dt>
                    <dd class="font-medium text-gray-800">{{ $reexportDeclaration->createdBy?->name ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        @if($reexportDeclaration->notes)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Ghi chú</h3>
            <p class="text-sm text-gray-700">{{ $reexportDeclaration->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Danh sách hàng xuất trả ({{ $reexportDeclaration->items->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">STT</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tên mặt hàng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Serial</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số lượng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reexportDeclaration->items as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->importItem?->equipment_name ?? '—' }}</td>
                        <td class="px-5 py-3 font-mono text-gray-600">{{ $item->serial?->serial_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->quantity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">Chưa có mặt hàng nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection