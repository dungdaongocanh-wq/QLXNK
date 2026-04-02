@extends('layouts.app')

@section('title', 'Chỉnh sửa Tờ khai Tạm nhập')

@section('content')
<div class="py-6 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('import-declarations.show', $importDeclaration) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Chỉnh sửa: {{ $importDeclaration->declaration_number }}</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('import-declarations.update', $importDeclaration) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tờ khai <span class="text-red-500">*</span></label>
                    <input type="text" name="declaration_number" value="{{ old('declaration_number', $importDeclaration->declaration_number) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('declaration_number') border-red-400 @enderror">
                    @error('declaration_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-400 @enderror">
                        <option value="active" {{ old('status', $importDeclaration->status) === 'active' ? 'selected' : '' }}>Hiệu lực</option>
                        <option value="extended" {{ old('status', $importDeclaration->status) === 'extended' ? 'selected' : '' }}>Gia hạn</option>
                        <option value="re_exported" {{ old('status', $importDeclaration->status) === 're_exported' ? 'selected' : '' }}>Đã tái xuất</option>
                        <option value="expired" {{ old('status', $importDeclaration->status) === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đăng ký <span class="text-red-500">*</span></label>
                    <input type="date" name="registration_date" value="{{ old('registration_date', $importDeclaration->registration_date?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('registration_date') border-red-400 @enderror">
                    @error('registration_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hạn tái xuất <span class="text-red-500">*</span></label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $importDeclaration->expiry_date?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expiry_date') border-red-400 @enderror">
                    @error('expiry_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhập khẩu <span class="text-red-500">*</span></label>
                    <input type="text" name="importer_name" value="{{ old('importer_name', $importDeclaration->importer_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('importer_name') border-red-400 @enderror">
                    @error('importer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes', $importDeclaration->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3 justify-end">
                <a href="{{ route('import-declarations.show', $importDeclaration) }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                    Hủy
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection