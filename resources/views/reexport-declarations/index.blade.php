@extends('layouts.app')

@section('title', 'Xuất trả')

@section('content')
<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tờ khai Xuất trả</h2>
        <a href="{{ route('reexport-declarations.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm mới
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số tờ khai</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Tờ khai tạm nhập</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Ngày đăng ký</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Số mặt hàng</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($declarations as $decl)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-blue-700">
                            <a href="{{ route('reexport-declarations.show', $decl) }}" class="hover:underline">
                                {{ $decl->declaration_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            @if($decl->importDeclaration)
                                <a href="{{ route('import-declarations.show', $decl->importDeclaration) }}" class="text-blue-600 hover:underline text-xs">
                                    {{ $decl->importDeclaration->declaration_number }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $decl->registration_date?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $decl->items->count() }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('reexport-declarations.show', $decl) }}" class="text-blue-600 hover:text-blue-800 font-medium">Xem</a>
                                <a href="{{ route('reexport-declarations.edit', $decl) }}" class="text-yellow-600 hover:text-yellow-800 font-medium">Sửa</a>
                                <form action="{{ route('reexport-declarations.destroy', $decl) }}" method="POST" onsubmit="return confirm('Xóa tờ khai này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Chưa có tờ khai xuất trả nào. <a href="{{ route('reexport-declarations.create') }}" class="text-blue-600 hover:underline">Thêm mới</a>
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
@endsection