@extends('layouts.app')

@section('title', 'Tạo Tờ khai Xuất trả')

@section('content')
<div class="py-6 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('reexport-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Tạo Tờ khai Xuất trả</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('reexport-declarations.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tờ khai <span class="text-red-500">*</span></label>
                    <input type="text" name="declaration_number" value="{{ old('declaration_number') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('declaration_number') border-red-400 @enderror"
                           placeholder="VD: C12345/XT/2025">
                    @error('declaration_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đăng ký <span class="text-red-500">*</span></label>
                    <input type="date" name="registration_date" value="{{ old('registration_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('registration_date') border-red-400 @enderror">
                    @error('registration_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tờ khai tạm nhập liên quan <span class="text-red-500">*</span></label>
                    <select name="import_declaration_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('import_declaration_id') border-red-400 @enderror">
                        <option value="">-- Chọn tờ khai tạm nhập --</option>
                        @foreach($imports as $import)
                        <option value="{{ $import->id }}" {{ old('import_declaration_id') == $import->id ? 'selected' : '' }}>
                            {{ $import->declaration_number }} — {{ $import->importer_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('import_declaration_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3 justify-end">
                <a href="{{ route('reexport-declarations.index') }}"
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