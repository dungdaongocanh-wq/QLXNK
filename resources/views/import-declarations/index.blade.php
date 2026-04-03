@extends('layouts.app')

@section('title', 'Tờ khai Tạm nhập')

@section('content')
<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tờ khai Tạm nhập</h2>
        <div class="flex items-center gap-3">
            <button id="openExcelModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>
            <a href="{{ route('import-declarations.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm mới
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số tờ khai</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Ngày đăng ký</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Hạn tái xuất</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số mặt hàng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($declarations as $decl)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-blue-700">
                            <a href="{{ route('import-declarations.show', $decl) }}" class="hover:underline">
                                {{ $decl->declaration_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $decl->registration_date ? $decl->registration_date->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $decl->expiry_date ? $decl->expiry_date->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $statusMap = [
                                    'active'      => ['bg-green-100 text-green-800',  'Hiệu lực'],
                                    'extended'    => ['bg-blue-100 text-blue-800',    'Gia hạn'],
                                    're_exported' => ['bg-gray-100 text-gray-700',    'Đã tái xuất'],
                                    'expired'     => ['bg-red-100 text-red-800',      'Hết hạn'],
                                ];
                                [$cls, $label] = $statusMap[$decl->status] ?? ['bg-gray-100 text-gray-700', $decl->status];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $cls }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $decl->items->count() }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('import-declarations.show', $decl) }}" class="text-blue-600 hover:text-blue-800 font-medium">Xem</a>
                                <a href="{{ route('import-declarations.edit', $decl) }}" class="text-yellow-600 hover:text-yellow-800 font-medium">Sửa</a>
                                <form action="{{ route('import-declarations.destroy', $decl) }}" method="POST" class="delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Chưa có tờ khai nào. <a href="{{ route('import-declarations.create') }}" class="text-blue-600 hover:underline">Thêm mới</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($declarations->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $declarations->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Excel Upload Modal --}}
<div id="excelModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Import file Excel tờ khai tạm nhập</h3>
            <button type="button" id="closeExcelModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('import-declarations.upload-excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <label class="cursor-pointer">
                    <span class="text-blue-600 hover:text-blue-800 font-medium">Chọn file Excel</span>
                    <input type="file" name="excel_file" id="importFileInput" accept=".xlsx,.xls" class="hidden" required>
                </label>
                <p id="importFileName" class="text-xs text-gray-500 mt-1">Hỗ trợ .xlsx, .xls</p>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" id="cancelExcelModal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                    Hủy
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const importModal = document.getElementById('excelModal');
    document.getElementById('openExcelModal').addEventListener('click', () => importModal.classList.remove('hidden'));
    document.getElementById('closeExcelModal').addEventListener('click', () => importModal.classList.add('hidden'));
    document.getElementById('cancelExcelModal').addEventListener('click', () => importModal.classList.add('hidden'));

    // Hiển thị tên file khi chọn
    document.getElementById('importFileInput').addEventListener('change', function () {
        const label = document.getElementById('importFileName');
        label.textContent = this.files[0] ? this.files[0].name : 'Hỗ trợ .xlsx, .xls';
    });

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm('Xóa tờ khai này?')) e.preventDefault();
        });
    });
</script>
@endpush