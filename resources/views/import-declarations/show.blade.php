@extends('layouts.app')

@section('title', 'Chi tiết Tờ khai Tạm nhập')

@section('content')
<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('import-declarations.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $importDeclaration->declaration_number }}</h2>
            @php
                $statusMap = [
                    'active'      => ['bg-green-100 text-green-800',  'Hiệu lực'],
                    'extended'    => ['bg-blue-100 text-blue-800',    'Gia hạn'],
                    're_exported' => ['bg-gray-100 text-gray-700',    'Đã tái xuất'],
                    'expired'     => ['bg-red-100 text-red-800',      'Hết hạn'],
                ];
                [$cls, $label] = $statusMap[$importDeclaration->status] ?? ['bg-gray-100 text-gray-700', $importDeclaration->status];
            @endphp
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $cls }}">{{ $label }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modal-extend').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Gia hạn
            </button>
            <a href="{{ route('import-declarations.edit', $importDeclaration) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Chỉnh sửa
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Thông tin --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Thông tin tờ khai --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin tờ khai</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số tờ khai</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->declaration_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ngày đăng ký</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->registration_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Hạn tái xuất</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->expiry_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Chi cục hải quan</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->customs_office ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mã loại hình</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->customs_type_code ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Hóa đơn</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->invoice_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tổng trị giá hóa đơn</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $importDeclaration->invoice_total_value ? number_format($importDeclaration->invoice_total_value, 2) . ' ' . ($importDeclaration->invoice_currency ?? '') : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số kiện</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $importDeclaration->package_quantity ? $importDeclaration->package_quantity . ' ' . $importDeclaration->package_unit : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Trọng lượng</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $importDeclaration->gross_weight ? number_format($importDeclaration->gross_weight, 3) . ' ' . $importDeclaration->weight_unit : '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Thông tin người nhập khẩu --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">Thông tin người nhập khẩu</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tên người nhập khẩu</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $importDeclaration->importer_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mã người nhập khẩu</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->importer_code ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Người xuất khẩu</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $importDeclaration->exporter_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nước xuất khẩu</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->exporter_country ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Số vận đơn</dt>
                    <dd class="font-medium text-gray-800">{{ $importDeclaration->bill_of_lading ?? '—' }}</dd>
                </div>
                {{-- Chi tiết khai trị giá D64 --}}
                @if($importDeclaration->value_detail)
                <div>
                    <dt class="text-gray-500 mb-1">Chi tiết khai trị giá</dt>
                    <dd class="text-gray-700 bg-gray-50 rounded p-2 text-xs leading-relaxed break-words">{{ $importDeclaration->value_detail }}</dd>
                </div>
                @endif
                {{-- Ghi chú G85 --}}
                @if($importDeclaration->customs_notes)
                <div>
                    <dt class="text-gray-500 mb-1">Ghi chú</dt>
                    <dd class="text-gray-700 bg-gray-50 rounded p-2 text-xs leading-relaxed">{{ $importDeclaration->customs_notes }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Danh sách mặt hàng --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Danh sách mặt hàng ({{ $importDeclaration->items->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-10">STT</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-24">Mã HS</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tên hàng hóa</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-28">Model</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-12">Xuất xứ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 w-16">SL nhập</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 w-16">SL xuất trả</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 w-16">Còn lại</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-32">Tờ khai xuất trả</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-24">Serial nhập</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Serial đã xuất trả</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($importDeclaration->items as $index => $item)
                    @php
                        $totalQty      = $item->quantity;
                        $rentedSerials = $item->serials->where('status', 'rented_out');
                        $rentedQty     = $rentedSerials->count();
                        $remainQty     = $totalQty - $rentedQty;
                        $exportDecls   = $rentedSerials->map(fn($s) => $s->currentExport)->filter()->unique('id');
                    @endphp
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $item->hs_code ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-800 text-xs leading-relaxed break-words whitespace-pre-wrap">{{ $item->equipment_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $item->model ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $item->origin_country ?? '—' }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $totalQty }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-orange-600">{{ $rentedQty }}</td>
                        <td class="px-4 py-3 text-center font-semibold {{ $remainQty > 0 ? 'text-green-600' : 'text-gray-400' }}">{{ $remainQty }}</td>
                        <td class="px-4 py-3 text-xs">
                            @forelse($exportDecls as $exp)
                                <a href="{{ route('export-declarations.show', $exp) }}" class="block text-blue-600 hover:underline font-mono">{{ $exp->declaration_number }}</a>
                            @empty
                                <span class="text-gray-400">—</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $item->serials->count() ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($rentedSerials->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($rentedSerials as $serial)
                                        <span class="px-1.5 py-0.5 bg-orange-50 border border-orange-200 text-orange-700 text-xs font-mono rounded">{{ $serial->serial_number }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-5 py-8 text-center text-gray-400">Chưa có mặt hàng nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Lịch sử gia hạn --}}
    @if($importDeclaration->extensionHistories?->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Lịch sử gia hạn ({{ $importDeclaration->extensionHistories->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">STT</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Hạn cũ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Hạn mới</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Số văn bản</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Ghi chú</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Ngày gia hạn</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($importDeclaration->extensionHistories as $i => $ext)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 text-red-500 font-medium">{{ \Carbon\Carbon::parse($ext->old_expiry_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-green-600 font-medium">{{ \Carbon\Carbon::parse($ext->new_expiry_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ext->extension_doc ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ext->notes ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ext->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Tờ khai tạm xuất liên quan --}}
    @if($importDeclaration->exportDeclarations->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tờ khai tạm xuất liên quan ({{ $importDeclaration->exportDeclarations->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số tờ khai</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Khách hàng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Ngày xuất</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($importDeclaration->exportDeclarations as $export)
                    @php
                        $expStatusMap = [
                            'active'             => ['bg-green-100 text-green-800',   'Hiệu lực'],
                            'partially_returned' => ['bg-yellow-100 text-yellow-800', 'Trả một phần'],
                            'fully_returned'     => ['bg-gray-100 text-gray-700',     'Đã hoàn trả'],
                            'overdue'            => ['bg-red-100 text-red-800',       'Quá hạn'],
                        ];
                        [$ec, $el] = $expStatusMap[$export->status] ?? ['bg-gray-100 text-gray-700', $export->status];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-blue-700">
                            <a href="{{ route('export-declarations.show', $export) }}" class="hover:underline">{{ $export->declaration_number }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $export->customer?->name ?? ($export->importer_name ?? '—') }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $export->registration_date?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $ec }}">{{ $el }}</span></td>
                        <td class="px-5 py-3"><a href="{{ route('export-declarations.show', $export) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Xem</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Modal Gia hạn --}}
<div id="modal-extend" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Gia hạn tờ khai</h3>
            <button onclick="document.getElementById('modal-extend').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('import-declarations.extend', $importDeclaration) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hạn hiện tại</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                        {{ $importDeclaration->expiry_date?->format('d/m/Y') }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hạn mới <span class="text-red-500">*</span></label>
                    <input type="date" name="new_expiry_date"
                        min="{{ $importDeclaration->expiry_date?->addDay()->format('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số văn bản</label>
                    <input type="text" name="extension_doc" placeholder="Số văn bản gia hạn"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea name="notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-extend').classList.add('hidden')"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">Hủy</button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">Gia hạn</button>
            </div>
        </form>
    </div>
</div>
@endsection