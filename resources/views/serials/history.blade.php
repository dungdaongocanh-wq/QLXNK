@extends('layouts.app')

@section('title', 'Lịch sử Serial')

@section('content')
<div class="py-6 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('serials.search') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Lịch sử Serial: <span class="font-mono text-blue-700">{{ $serial->serial_number }}</span></h2>
    </div>

    {{-- Basic info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin thiết bị</h3>
        <dl class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
            <div>
                <dt class="text-gray-500">Serial Number</dt>
                <dd class="font-mono font-semibold text-gray-800 mt-0.5">{{ $serial->serial_number }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tên thiết bị</dt>
                <dd class="font-medium text-gray-800 mt-0.5">{{ $serial->importItem?->equipment_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Model</dt>
                <dd class="font-medium text-gray-800 mt-0.5">{{ $serial->importItem?->model ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tình trạng</dt>
                <dd class="mt-0.5">
                    @php
                        $statusMap = [
                            'in_stock'    => ['bg-green-100 text-green-800',  'Trong kho'],
                            'rented_out'  => ['bg-blue-100 text-blue-800',    'Đang cho thuê'],
                            're_exported' => ['bg-gray-100 text-gray-700',    'Đã tái xuất'],
                        ];
                        [$cls, $label] = $statusMap[$serial->status] ?? ['bg-gray-100 text-gray-700', $serial->status];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $cls }}">{{ $label }}</span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Tờ khai tạm nhập</dt>
                <dd class="mt-0.5">
                    @if($serial->importItem?->importDeclaration)
                    <a href="{{ route('import-declarations.show', $serial->importItem->importDeclaration) }}" class="text-blue-600 hover:underline text-sm">
                        {{ $serial->importItem->importDeclaration->declaration_number }}
                    </a>
                    @else
                    —
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-5 pb-3 border-b border-gray-100">Lịch sử di chuyển</h3>

        <div class="relative">
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

            <div class="space-y-6">
                {{-- Import event --}}
                @if($serial->importItem?->importDeclaration)
                <div class="relative flex gap-4 pl-11">
                    <div class="absolute left-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div class="flex-1 bg-blue-50 border border-blue-100 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-blue-800 text-sm">Nhập khẩu</span>
                            <span class="text-xs text-gray-500">{{ $serial->importItem->importDeclaration->registration_date?->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm text-blue-700">Tờ khai: {{ $serial->importItem->importDeclaration->declaration_number }}</p>
                        <p class="text-xs text-blue-600 mt-0.5">{{ $serial->importItem->importDeclaration->importer_name }}</p>
                    </div>
                </div>
                @endif

                {{-- Export events --}}
                @foreach($serial->exportSerialItems as $exportItem)
                <div class="relative flex gap-4 pl-11">
                    <div class="absolute left-0 w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div class="flex-1 bg-indigo-50 border border-indigo-100 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-indigo-800 text-sm">Xuất khẩu tạm thời</span>
                            <span class="text-xs text-gray-500">{{ $exportItem->exportDeclaration?->registration_date?->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm text-indigo-700">Tờ khai: {{ $exportItem->exportDeclaration?->declaration_number }}</p>
                        @if($exportItem->exportDeclaration?->customer)
                        <p class="text-xs text-indigo-600 mt-0.5">Khách hàng: {{ $exportItem->exportDeclaration->customer->name }}</p>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- Reimport events --}}
                @foreach($serial->reimportSerialItems as $reimportItem)
                <div class="relative flex gap-4 pl-11">
                    <div class="absolute left-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <div class="flex-1 bg-green-50 border border-green-100 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-green-800 text-sm">Tái nhập</span>
                            <span class="text-xs text-gray-500">{{ $reimportItem->reimportRecord?->created_at?->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm text-green-700">Khách hàng đã trả hàng về</p>
                    </div>
                </div>
                @endforeach

                @if($serial->exportSerialItems->isEmpty() && $serial->reimportSerialItems->isEmpty() && !$serial->importItem)
                <div class="pl-11 text-sm text-gray-400">Chưa có lịch sử di chuyển</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection