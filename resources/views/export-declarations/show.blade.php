@extends('layouts.app')

@section('title', 'Chi tiết Tờ khai Tạm xuất')

@section('content')
<div class="py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('export-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $exportDeclaration->declaration_number }}</h2>
            @php
                $statusMap = [
                    'active'             => ['bg-green-100 text-green-800',   'Hiệu lực'],
                    'partially_returned' => ['bg-yellow-100 text-yellow-800', 'Trả một phần'],
                    'fully_returned'     => ['bg-gray-100 text-gray-700',     'Đã hoàn trả'],
                    'overdue'            => ['bg-red-100 text-red-800',       'Quá hạn'],
                ];
                [$cls, $label] = $statusMap[$exportDeclaration->status] ?? ['bg-gray-100 text-gray-700', $exportDeclaration->status];
            @endphp
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $cls }}">{{ $label }}</span>
        </div>
        <a href="{{ route('export-declarations.edit', $exportDeclaration) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Chỉnh sửa
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Thông tin tờ khai + các bên --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Thông tin tờ khai --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin tờ khai</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số tờ khai</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->declaration_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mã loại hình</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->customs_type_code ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mã PL kiểm tra</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->inspection_code ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Chi cục hải quan</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->customs_office ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ngày đăng ký</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->registration_date?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Hạn tái nhập</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->expiry_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số hóa đơn</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->invoice_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ngày phát hành HĐ</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->invoice_date ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tổng trị giá</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $exportDeclaration->total_value ? number_format($exportDeclaration->total_value, 2) . ' ' . $exportDeclaration->currency : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số kiện / Trọng lượng</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $exportDeclaration->package_quantity ? $exportDeclaration->package_quantity . ' ' . $exportDeclaration->package_unit : '—' }}
                        /
                        {{ $exportDeclaration->gross_weight ? number_format($exportDeclaration->gross_weight, 3) . ' ' . $exportDeclaration->weight_unit : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tổng dòng hàng</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->total_item_lines ?? '—' }}</dd>
                </div>
                @if($exportDeclaration->marks_and_numbers)
                <div>
                    <dt class="text-gray-500 mb-1">Ký hiệu và số hiệu</dt>
                    <dd class="text-gray-700 bg-gray-50 rounded p-2 text-xs leading-relaxed break-words">{{ $exportDeclaration->marks_and_numbers }}</dd>
                </div>
                @endif
                @if($exportDeclaration->export_notes)
                <div>
                    <dt class="text-gray-500 mb-1">Ghi chú</dt>
                    <dd class="text-gray-700 bg-gray-50 rounded p-2 text-xs">{{ $exportDeclaration->export_notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Thông tin các bên --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin các bên</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Công ty xuất (bên cho thuê)</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $exportDeclaration->exporter_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">MST</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->exporter_tax_code ?? '—' }}</dd>
                </div>
                <div class="border-t border-gray-100 pt-3"></div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Công ty nhập (bên thuê)</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $exportDeclaration->importer_name ?? ($exportDeclaration->customer?->name ?? '—') }}</dd>
                </div>
                @if($exportDeclaration->importer_address)
                <div>
                    <dt class="text-gray-500 mb-1">Địa chỉ</dt>
                    <dd class="text-gray-700 text-xs leading-relaxed">{{ $exportDeclaration->importer_address }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mã nước</dt>
                    <dd class="font-medium text-gray-800">{{ $exportDeclaration->importer_country ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Danh sách mặt hàng từ Excel --}}
    @if($exportDeclaration->items->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">
                Danh sách mặt hàng
                <span class="text-gray-400 font-normal text-sm">(từ file Excel)</span>
                <span class="ml-1 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $exportDeclaration->items->count() }}</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-10">STT</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-24">Mã HS</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Mô tả hàng hóa</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-32">Model</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-16">Xuất xứ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-12">SL</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-28">Đơn giá</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-28">Trị giá</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($exportDeclaration->items as $i => $item)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $item->hs_code ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700 text-xs leading-relaxed break-words whitespace-pre-wrap">{{ $item->description ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $item->model ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $item->origin_country ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $item->unit_price ? number_format($item->unit_price, 2) . ' ' . $item->currency : '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $item->total_value ? number_format($item->total_value, 2) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Serial đã gắn + Form gắn serial --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                Serial xuất kho
                <span class="ml-1 px-2 py-0.5 bg-orange-100 text-orange-700 text-xs rounded-full">{{ $exportDeclaration->serialItems->count() }}</span>
            </h3>
            @if($availableSerials->isNotEmpty())
            <button onclick="document.getElementById('section-attach').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Gắn Serial
            </button>
            @endif
        </div>

        {{-- Form gắn serial --}}
        <div id="section-attach" class="hidden border-b border-blue-100 bg-blue-50/50 px-5 py-4">
            <form action="{{ route('export-declarations.attach-serials', $exportDeclaration) }}" method="POST">
                @csrf
                <div class="mb-3 flex items-center gap-3">
                    <input type="text" id="serialSearch"
                        placeholder="Tìm serial number, model, tên thiết bị..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $availableSerials->count() }} trong kho</span>
                </div>

                <div class="max-h-56 overflow-y-auto border border-gray-200 rounded-lg bg-white divide-y divide-gray-100" id="serialList">
                    @foreach($availableSerials as $serial)
                    <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 cursor-pointer serial-row"
                        data-search="{{ strtolower($serial->serial_number . ' ' . ($serial->importItem?->model ?? '') . ' ' . ($serial->importItem?->equipment_name ?? '')) }}">
                        <input type="checkbox" name="serial_ids[]" value="{{ $serial->id }}"
                            class="w-4 h-4 text-blue-600 rounded border-gray-300">
                        <div class="flex-1 min-w-0 flex items-center gap-2">
                            <span class="font-mono font-semibold text-blue-700 text-sm">{{ $serial->serial_number }}</span>
                            @if($serial->importItem?->model)
                                <span class="text-gray-500 text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ $serial->importItem->model }}</span>
                            @endif
                            @if($serial->importItem?->equipment_name)
                                <span class="text-gray-400 text-xs truncate">{{ Str::limit($serial->importItem->equipment_name, 50) }}</span>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>

                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-blue-600">
                        Chọn tất cả
                    </label>
                    <div class="flex items-center gap-3">
                        <span id="selectedCount" class="text-xs text-gray-500">0 đã chọn</span>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                            Gắn vào tờ khai
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Bảng serial đã gắn --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">STT</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Serial Number</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tên thiết bị</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Model</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Ngày tái nhập</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($exportDeclaration->serialItems as $index => $item)
                    @php
                        $sc = match($item->serial?->status) {
                            'in_stock'    => 'bg-green-100 text-green-700',
                            'rented_out'  => 'bg-orange-100 text-orange-700',
                            're_exported' => 'bg-gray-100 text-gray-600',
                            default       => 'bg-gray-100 text-gray-600',
                        };
                        $sl = match($item->serial?->status) {
                            'in_stock'    => 'Trong kho',
                            'rented_out'  => 'Đang xuất',
                            're_exported' => 'Đã trả',
                            default       => $item->serial?->status ?? '—',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-mono font-semibold text-blue-700">{{ $item->serial?->serial_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $item->serial?->importItem?->equipment_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $item->serial?->importItem?->model ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $sc }}">{{ $sl }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $item->returned_at ? \Carbon\Carbon::parse($item->returned_at)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if(!$item->returned_at)
                            <form action="{{ route('export-declarations.detach-serial', [$exportDeclaration, $item->serial_id]) }}"
                                  method="POST" class="detach-form inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Gỡ</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                            Chưa có serial nào. Nhấn <strong>Gắn Serial</strong> để thêm.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Lịch sử tái nhập --}}
    @if($exportDeclaration->reimportRecords->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Lịch sử tái nhập ({{ $exportDeclaration->reimportRecords->count() }})</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($exportDeclaration->reimportRecords as $reimport)
            <div class="px-5 py-3 text-sm flex justify-between items-center">
                <span class="font-medium text-gray-800">Tái nhập ngày {{ $reimport->created_at->format('d/m/Y H:i') }}</span>
                <span class="text-gray-500">{{ $reimport->serialItems->count() }} serial</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('serialSearch')?.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.serial-row').forEach(row => {
            row.style.display = !q || row.dataset.search.includes(q) ? '' : 'none';
        });
        updateCount();
    });

    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.serial-row').forEach(row => {
            if (row.style.display !== 'none') {
                row.querySelector('input[type="checkbox"]').checked = this.checked;
            }
        });
        updateCount();
    });

    document.querySelectorAll('input[name="serial_ids[]"]').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    function updateCount() {
        const n = document.querySelectorAll('input[name="serial_ids[]"]:checked').length;
        const el = document.getElementById('selectedCount');
        if (el) el.textContent = n + ' đã chọn';
    }

    document.querySelectorAll('.detach-form').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm('Gỡ serial này? Serial sẽ trở về trạng thái Trong kho.')) e.preventDefault();
        });
    });
</script>
@endpush