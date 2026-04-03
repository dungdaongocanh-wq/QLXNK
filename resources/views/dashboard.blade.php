@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tổng quan hệ thống</h2>

    {{-- Tờ khai Tạm nhập --}}
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-1 h-6 bg-blue-600 rounded-full inline-block"></span>
            Tờ khai Tạm nhập
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tổng số</p>
                        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $importStats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('import-declarations.index') }}" class="text-xs text-blue-500 hover:text-blue-700 mt-3 inline-block">Xem tất cả →</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Đang hiệu lực</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $importStats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Sắp hết hạn 30 ngày</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $importStats['expiring_30d'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Sắp hết hạn 7 ngày</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ $importStats['expiring_7d'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tờ khai Tạm xuất --}}
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-1 h-6 bg-indigo-600 rounded-full inline-block"></span>
            Tờ khai Tạm xuất
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tổng số</p>
                        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $exportStats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('export-declarations.index') }}" class="text-xs text-blue-500 hover:text-blue-700 mt-3 inline-block">Xem tất cả →</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Đang hiệu lực</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $exportStats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Sắp hết hạn 30 ngày</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $exportStats['expiring_30d'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Quá hạn</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ $exportStats['overdue'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert tables --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Import alerts --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <h3 class="font-semibold text-gray-800">Tờ khai Tạm nhập sắp hết hạn</h3>
            </div>
            @if($importAlerts->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-5 py-3 text-left font-medium">Số tờ khai</th>
                                <th class="px-5 py-3 text-left font-medium">Ngày hết hạn</th>
                                <th class="px-5 py-3 text-left font-medium">Trạng thái</th>
                                <th class="px-5 py-3 text-left font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($importAlerts as $alert)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-blue-700">{{ $alert->declaration_number }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $alert->expiry_date->format('d/m/Y') }}</td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">{{ $alert->status }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('import-declarations.show', $alert) }}" class="text-blue-600 hover:text-blue-800 text-xs">Xem</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-8 text-center text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Không có tờ khai sắp hết hạn
                </div>
            @endif
        </div>

        {{-- Export alerts --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <h3 class="font-semibold text-gray-800">Tờ khai Tạm xuất sắp hết hạn</h3>
            </div>
            @if($exportAlerts->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-5 py-3 text-left font-medium">Số tờ khai</th>
                                <th class="px-5 py-3 text-left font-medium">Khách hàng</th>
                                <th class="px-5 py-3 text-left font-medium">Ngày hết hạn</th>
                                <th class="px-5 py-3 text-left font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($exportAlerts as $alert)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-blue-700">{{ $alert->declaration_number }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $alert->customer?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $alert->expiry_date->format('d/m/Y') }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('export-declarations.show', $alert) }}" class="text-blue-600 hover:text-blue-800 text-xs">Xem</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-8 text-center text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Không có tờ khai sắp hết hạn
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
