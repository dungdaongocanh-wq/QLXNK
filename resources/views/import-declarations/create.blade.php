@extends('layouts.app')

@section('title', 'Tạo Tờ khai Tạm nhập')

@section('content')
<div class="py-6 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('import-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Tạo Tờ khai Tạm nhập</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('import-declarations.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tờ khai <span class="text-red-500">*</span></label>
                    <input type="text" name="declaration_number" value="{{ old('declaration_number') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('declaration_number') border-red-400 @enderror"
                           placeholder="VD: A12345/KD/2025">
                    @error('declaration_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đăng ký <span class="text-red-500">*</span></label>
                    <input type="date" name="registration_date" value="{{ old('registration_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('registration_date') border-red-400 @enderror">
                    @error('registration_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hạn tái xuất <span class="text-red-500">*</span></label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expiry_date') border-red-400 @enderror">
                    @error('expiry_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chi cục hải quan</label>
                    <input type="text" name="customs_office" value="{{ old('customs_office') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="VD: Chi cục HQ cửa khẩu Nội Bài">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhập khẩu <span class="text-red-500">*</span></label>
                    <input type="text" name="importer_name" value="{{ old('importer_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('importer_name') border-red-400 @enderror"
                           placeholder="Tên công ty hoặc cá nhân nhập khẩu">
                    @error('importer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã số thuế</label>
                    <input type="text" name="importer_tax_code" value="{{ old('importer_tax_code') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="MST nhập khẩu">
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3 justify-end">
                <a href="{{ route('import-declarations.index') }}"
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

    {{-- Excel upload alternative --}}
    <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-5">
        <h3 class="font-semibold text-green-800 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Hoặc import từ file Excel
        </h3>
        <p class="text-sm text-green-700 mb-3">Upload file Excel tờ khai để tự động điền thông tin và danh sách hàng hóa.</p>
        <form action="{{ route('import-declarations.upload-excel') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <input type="file" name="excel_file" accept=".xlsx,.xls"
                   class="flex-1 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-green-600 file:text-white hover:file:bg-green-700">
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                Import
            </button>
        </form>
    </div>
</div>
@endsection