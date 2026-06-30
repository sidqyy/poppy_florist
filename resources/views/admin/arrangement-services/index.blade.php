@extends('layouts.app')

@section('title', 'Master Jasa Rangkai')
@section('page_title', 'Master Jasa Rangkai')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Master Jasa Rangkai</h3>
        <p class="text-gray-500 text-sm mt-1">Kelola harga jasa rangkai berdasarkan jumlah item.</p>
    </div>

    <a href="{{ route('admin.arrangement-services.create') }}"
       class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg shadow-sm font-bold">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Jasa
    </a>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Min</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Max</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Harga</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($services as $service)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-6 font-bold text-gray-800">
                        {{ $service->name }}
                    </td>

                    <td class="py-4 px-6 text-gray-600">
                        {{ $service->min_item }}
                    </td>

                    <td class="py-4 px-6 text-gray-600">
                        {{ $service->max_item ?? '150+' }}
                    </td>

                    <td class="py-4 px-6 font-bold text-florist-600">
                        Rp {{ number_format($service->price, 0, ',', '.') }}
                    </td>

                    <td class="py-4 px-6">
                        @if($service->is_premium)
                            <span class="px-2 py-1 bg-purple-50 text-purple-600 rounded-full text-xs font-bold border border-purple-100">
                                Premium
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-bold border border-gray-100">
                                Normal
                            </span>
                        @endif
                    </td>

                    <td class="py-4 px-6">
                        @if($service->is_active)
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold border border-green-100">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold border border-red-100">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    <td class="py-4 px-6">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.arrangement-services.edit', $service->id) }}"
                               class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>

                            <form action="{{ route('admin.arrangement-services.destroy', $service->id) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        onclick="return confirm('Hapus jasa rangkai ini?')"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-hands-holding-heart text-4xl mb-3 block text-gray-300"></i>
                        <p>Belum ada data jasa rangkai.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection