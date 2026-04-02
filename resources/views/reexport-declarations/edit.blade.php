@extends('layouts.app')

@section('title', 'Chỉnh sửa Tờ khai Xuất trả')

@section('content')
<div class="py-6 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('reexport-declarations.show', $reexportDeclaration) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Chỉnh sửa: {{ $reexportDeclaration->declaration_number }}</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('reexport-declarations.update', $reexportDeclaration) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea name="notes" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $reexportDeclaration->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3 justify-end">
                <a href="{{ route('reexport-declarations.show', $reexportDeclaration) }}"
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