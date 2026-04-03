@extends('layouts.app')

@section('title', 'Chỉnh sửa Tờ khai Tạm xuất')

@section('content')
<div class="py-6 max-w-5xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('export-declarations.show', $exportDeclaration) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Chỉnh sửa: {{ $exportDeclaration->declaration_number }}</h2>
    </div>

    @if($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('export-declarations.update', $exportDeclaration) }}" method="POST">
        @csrf @method('PUT')

        {{-- Thông tin tờ khai --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin tờ khai</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tờ khai <span class="text-red-500">*</span></label>
                    <input type="text" name="declaration_number"
                        value="{{ old('declaration_number', $exportDeclaration->declaration_number) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('declaration_number') border-red-400 @enderror">
                    @error('declaration_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã loại hình</label>
                    <input type="text" name="customs_type_code"
                        value="{{ old('customs_type_code', $exportDeclaration->customs_type_code) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã PL kiểm tra</label>
                    <input type="text" name="inspection_code"
                        value="{{ old('inspection_code', $exportDeclaration->inspection_code) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chi cục hải quan tiếp nhận</label>
                    <input type="text" name="customs_office"
                        value="{{ old('customs_office', $exportDeclaration->customs_office) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="active"             {{ old('status', $exportDeclaration->status) === 'active'             ? 'selected' : '' }}>Hiệu lực</option>
                        <option value="partially_returned" {{ old('status', $exportDeclaration->status) === 'partially_returned' ? 'selected' : '' }}>Trả một phần</option>
                        <option value="fully_returned"     {{ old('status', $exportDeclaration->status) === 'fully_returned'     ? 'selected' : '' }}>Đã hoàn trả</option>
                        <option value="overdue"            {{ old('status', $exportDeclaration->status) === 'overdue'            ? 'selected' : '' }}>Quá hạn</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đăng ký <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="registration_date"
                        value="{{ old('registration_date', $exportDeclaration->registration_date?->format('Y-m-d\TH:i')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('registration_date') border-red-400 @enderror">
                    @error('registration_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hạn tái nhập/tái xuất <span class="text-red-500">*</span></label>
                    <input type="date" name="expiry_date"
                        value="{{ old('expiry_date', $exportDeclaration->expiry_date?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('expiry_date') border-red-400 @enderror">
                    @error('expiry_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Thông tin hóa đơn --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin hóa đơn</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số hóa đơn</label>
                    <input type="text" name="invoice_number"
                        value="{{ old('invoice_number', $exportDeclaration->invoice_number) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày phát hành</label>
                    <input type="text" name="invoice_date"
                        value="{{ old('invoice_date', $exportDeclaration->invoice_date) }}"
                        placeholder="vd: 10/03/2026"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tổng trị giá</label>
                    <input type="number" step="0.01" name="total_value"
                        value="{{ old('total_value', $exportDeclaration->total_value) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại tiền</label>
                    <select name="currency"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach(['USD','EUR','JPY','VND','GBP','SGD','KRW'] as $cur)
                        <option value="{{ $cur }}" {{ old('currency', $exportDeclaration->currency) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- Hàng hóa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin hàng hóa</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng kiện</label>
                    <input type="number" name="package_quantity"
                        value="{{ old('package_quantity', $exportDeclaration->package_quantity) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị kiện</label>
                    <input type="text" name="package_unit"
                        value="{{ old('package_unit', $exportDeclaration->package_unit) }}"
                        placeholder="PK, CTN..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tổng trọng lượng</label>
                    <input type="number" step="0.001" name="gross_weight"
                        value="{{ old('gross_weight', $exportDeclaration->gross_weight) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị trọng lượng</label>
                    <input type="text" name="weight_unit"
                        value="{{ old('weight_unit', $exportDeclaration->weight_unit) }}"
                        placeholder="KGM, LBR..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ký hiệu và số hiệu (H47)</label>
                    <textarea name="marks_and_numbers" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('marks_and_numbers', $exportDeclaration->marks_and_numbers) }}</textarea>
                </div>

            </div>
        </div>

        {{-- Thông tin các bên --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin các bên</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên công ty xuất (bên cho thuê)</label>
                    <input type="text" name="exporter_name"
                        value="{{ old('exporter_name', $exportDeclaration->exporter_name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">MST công ty xuất</label>
                    <input type="text" name="exporter_tax_code"
                        value="{{ old('exporter_tax_code', $exportDeclaration->exporter_tax_code) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên công ty nhập (bên thuê)</label>
                    <input type="text" name="importer_name"
                        value="{{ old('importer_name', $exportDeclaration->importer_name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã nước</label>
                    <input type="text" name="importer_country"
                        value="{{ old('importer_country', $exportDeclaration->importer_country) }}"
                        placeholder="VN, DE, KR..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ công ty nhập</label>
                    <input type="text" name="importer_address"
                        value="{{ old('importer_address', $exportDeclaration->importer_address) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng (nếu có trong hệ thống)</label>
                    <select name="customer_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">— Không chọn —</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id', $exportDeclaration->customer_id) == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- Ghi chú --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Ghi chú</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phần ghi chú (F64)</label>
                    <textarea name="export_notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('export_notes', $exportDeclaration->export_notes) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú nội bộ</label>
                    <textarea name="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('notes', $exportDeclaration->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('export-declarations.show', $exportDeclaration) }}"
               class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                Hủy
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                Lưu thay đổi
            </button>
        </div>

    </form>
</div>
@endsection