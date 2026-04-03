@extends('layouts.app')

@section('title', 'Tạo Tờ khai Tạm xuất')

@section('content')
<div class="py-6 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('export-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Tạo Tờ khai Tạm xuất</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('export-declarations.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tờ khai <span class="text-red-500">*</span></label>
                    <input type="text" name="declaration_number" value="{{ old('declaration_number') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('declaration_number') border-red-400 @enderror"
                           placeholder="VD: B12345/TX/2025">
                    @error('declaration_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng</label>
                    <select name="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Chọn khách hàng --</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đăng ký <span class="text-red-500">*</span></label>
                    <input type="date" name="registration_date" value="{{ old('registration_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('registration_date') border-red-400 @enderror">
                    @error('registration_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hạn tái nhập <span class="text-red-500">*</span></label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expiry_date') border-red-400 @enderror">
                    @error('expiry_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tờ khai tạm nhập liên quan</label>
                    <select name="import_declaration_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Chọn tờ khai tạm nhập --</option>
                        @foreach($imports as $import)
                        <option value="{{ $import->id }}" {{ old('import_declaration_id') == $import->id ? 'selected' : '' }}>
                            {{ $import->declaration_number }} ({{ $import->expiry_date?->format('d/m/Y') }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh sách serial <span class="text-red-500">*</span></label>
                    @error('serials')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror
                    <div class="border border-gray-200 rounded-lg p-3 max-h-48 overflow-y-auto space-y-1">
                        @forelse($availableSerials as $serial)
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 px-2 py-1 rounded">
                            <input type="checkbox" name="serials[]" value="{{ $serial->id }}"
                                   {{ in_array($serial->id, old('serials', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600">
                            <span class="font-mono">{{ $serial->serial_number }}</span>
                            <span class="text-gray-500 text-xs">— {{ $serial->importItem?->equipment_name }}</span>
                        </label>
                        @empty
                        <p class="text-gray-400 text-sm text-center py-2">Không có serial nào trong kho</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3 justify-end">
                <a href="{{ route('export-declarations.index') }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                    Hủy
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Tạo tờ khai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection