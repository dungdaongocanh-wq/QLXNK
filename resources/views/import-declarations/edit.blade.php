@extends('layouts.app')

@section('title', 'Chỉnh sửa Tờ khai Tạm nhập')

@section('content')
<div class="py-6 max-w-5xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('import-declarations.show', $importDeclaration) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Chỉnh sửa: {{ $importDeclaration->declaration_number }}</h2>
    </div>

    @if($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('import-declarations.update', $importDeclaration) }}" method="POST" id="editForm">
        @csrf @method('PUT')

        {{-- Thông tin tờ khai --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin tờ khai</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tờ khai <span class="text-red-500">*</span></label>
                    <input type="text" name="declaration_number" value="{{ old('declaration_number', $importDeclaration->declaration_number) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('declaration_number') border-red-400 @enderror">
                    @error('declaration_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="active"      {{ old('status', $importDeclaration->status) === 'active'      ? 'selected' : '' }}>Hiệu lực</option>
                        <option value="extended"    {{ old('status', $importDeclaration->status) === 'extended'    ? 'selected' : '' }}>Gia hạn</option>
                        <option value="re_exported" {{ old('status', $importDeclaration->status) === 're_exported' ? 'selected' : '' }}>Đã tái xuất</option>
                        <option value="expired"     {{ old('status', $importDeclaration->status) === 'expired'     ? 'selected' : '' }}>Hết hạn</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã loại hình</label>
                    <input type="text" name="customs_type_code" value="{{ old('customs_type_code', $importDeclaration->customs_type_code) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đăng ký <span class="text-red-500">*</span></label>
                    <input type="date" name="registration_date" value="{{ old('registration_date', $importDeclaration->registration_date?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('registration_date') border-red-400 @enderror">
                    @error('registration_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hạn tái xuất <span class="text-red-500">*</span></label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $importDeclaration->expiry_date?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('expiry_date') border-red-400 @enderror">
                    @error('expiry_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chi cục hải quan</label>
                    <input type="text" name="customs_office" value="{{ old('customs_office', $importDeclaration->customs_office) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Hóa đơn & Vận chuyển --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Hóa đơn & Vận chuyển</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số hóa đơn</label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number', $importDeclaration->invoice_number) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày phát hành</label>
                    <input type="text" name="invoice_date" value="{{ old('invoice_date', $importDeclaration->invoice_date) }}"
                        placeholder="vd: 10/03/2026"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tổng trị giá</label>
                    <input type="number" step="0.01" name="total_invoice_value" value="{{ old('total_invoice_value', $importDeclaration->total_invoice_value) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại tiền</label>
                    <select name="currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach(['USD','EUR','JPY','VND','GBP','SGD','KRW','CIF'] as $cur)
                        <option value="{{ $cur }}" {{ old('currency', $importDeclaration->currency ?? $importDeclaration->invoice_currency) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số vận đơn</label>
                    <input type="text" name="bill_of_lading" value="{{ old('bill_of_lading', $importDeclaration->bill_of_lading) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số kiện</label>
                    <input type="number" name="package_quantity" value="{{ old('package_quantity', $importDeclaration->package_quantity) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị kiện</label>
                    <input type="text" name="package_unit" value="{{ old('package_unit', $importDeclaration->package_unit) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trọng lượng</label>
                    <div class="flex gap-1">
                        <input type="number" step="0.001" name="gross_weight" value="{{ old('gross_weight', $importDeclaration->gross_weight) }}"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="weight_unit" value="{{ old('weight_unit', $importDeclaration->weight_unit) }}"
                            placeholder="KGM"
                            class="w-16 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Thông tin các bên --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin các bên</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhập khẩu</label>
                    <input type="text" name="importer_name" value="{{ old('importer_name', $importDeclaration->importer_name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã người nhập khẩu (MST)</label>
                    <input type="text" name="importer_code" value="{{ old('importer_code', $importDeclaration->importer_code) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Người xuất khẩu</label>
                    <input type="text" name="exporter_name" value="{{ old('exporter_name', $importDeclaration->exporter_name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nước xuất khẩu</label>
                    <input type="text" name="exporter_country" value="{{ old('exporter_country', $importDeclaration->exporter_country) }}"
                        placeholder="VN, DE, KR..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chi tiết khai trị giá (D64)</label>
                    <textarea name="value_detail" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('value_detail', $importDeclaration->value_detail) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú (G85)</label>
                    <textarea name="customs_notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('customs_notes', $importDeclaration->customs_notes) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú nội bộ</label>
                    <textarea name="notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('notes', $importDeclaration->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Danh sách mặt hàng --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Danh sách mặt hàng</h3>
                <button type="button" id="btnAddItem"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm mặt hàng
                </button>
            </div>

            <div id="items-container" class="space-y-4">
                @foreach($importDeclaration->items as $idx => $item)
                <div class="item-block border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="{{ $idx }}">
                    <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-gray-700">Mặt hàng #<span class="item-num">{{ $idx + 1 }}</span></span>
                        <button type="button" class="btn-remove-item text-red-500 hover:text-red-700 text-xs font-medium">Xóa</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Mã HS</label>
                            <input type="text" name="items[{{ $idx }}][hs_code]" value="{{ old("items.$idx.hs_code", $item->hs_code) }}"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tên hàng hóa</label>
                            <input type="text" name="items[{{ $idx }}][equipment_name]" value="{{ old("items.$idx.equipment_name", $item->equipment_name) }}"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Model</label>
                            <input type="text" name="items[{{ $idx }}][model]" value="{{ old("items.$idx.model", $item->model) }}"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Xuất xứ</label>
                            <input type="text" name="items[{{ $idx }}][origin_country]" value="{{ old("items.$idx.origin_country", $item->origin_country) }}"
                                placeholder="VN, DE..."
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Số lượng</label>
                            <input type="number" name="items[{{ $idx }}][quantity]" value="{{ old("items.$idx.quantity", $item->quantity) }}" min="0"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Đơn vị</label>
                            <input type="text" name="items[{{ $idx }}][quantity_unit]" value="{{ old("items.$idx.quantity_unit", $item->quantity_unit) }}"
                                placeholder="PCE, SET..."
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Đơn giá</label>
                            <input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" value="{{ old("items.$idx.unit_price", $item->unit_price) }}"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tổng trị giá</label>
                            <input type="number" step="0.01" name="items[{{ $idx }}][total_value]" value="{{ old("items.$idx.total_value", $item->total_value) }}"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-2 lg:col-span-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Serial numbers
                                <span class="text-gray-400 font-normal">(mỗi serial 1 dòng hoặc phân cách bằng dấu phẩy/gạch chéo)</span>
                            </label>
                            <textarea name="items[{{ $idx }}][serials]" rows="2"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 font-mono"
                                placeholder="0205225&#10;0205224">{{ old("items.$idx.serials", $item->serials->pluck('serial_number')->join("\n")) }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('import-declarations.show', $importDeclaration) }}"
               class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">Hủy</a>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Lưu thay đổi</button>
        </div>

    </form>
</div>

{{-- Template item mới --}}
<template id="item-template">
    <div class="item-block border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="__IDX__">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-700">Mặt hàng #<span class="item-num">__NUM__</span></span>
            <button type="button" class="btn-remove-item text-red-500 hover:text-red-700 text-xs font-medium">Xóa</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Mã HS</label>
                <input type="text" name="items[__IDX__][hs_code]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Tên hàng hóa</label>
                <input type="text" name="items[__IDX__][equipment_name]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Model</label>
                <input type="text" name="items[__IDX__][model]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Xuất xứ</label>
                <input type="text" name="items[__IDX__][origin_country]" placeholder="VN, DE..." class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Số lượng</label>
                <input type="number" name="items[__IDX__][quantity]" value="1" min="0" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Đơn vị</label>
                <input type="text" name="items[__IDX__][quantity_unit]" placeholder="PCE, SET..." class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Đơn giá</label>
                <input type="number" step="0.01" name="items[__IDX__][unit_price]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tổng trị giá</label>
                <input type="number" step="0.01" name="items[__IDX__][total_value]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Serial numbers <span class="text-gray-400 font-normal">(mỗi serial 1 dòng)</span></label>
                <textarea name="items[__IDX__][serials]" rows="2"
                    class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 font-mono"></textarea>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    let itemCount = {{ $importDeclaration->items->count() }};

    document.getElementById('btnAddItem').addEventListener('click', () => {
        const tmpl = document.getElementById('item-template').innerHTML;
        const html = tmpl.replaceAll('__IDX__', itemCount).replaceAll('__NUM__', itemCount + 1);
        const div  = document.createElement('div');
        div.innerHTML = html;
        document.getElementById('items-container').appendChild(div.firstElementChild);
        itemCount++;
        bindRemove();
    });

    function bindRemove() {
        document.querySelectorAll('.btn-remove-item').forEach(btn => {
            btn.onclick = function () {
                if (!confirm('Xóa mặt hàng này?')) return;
                this.closest('.item-block').remove();
                document.querySelectorAll('.item-num').forEach((el, i) => el.textContent = i + 1);
            };
        });
    }

    bindRemove();
</script>
@endpush