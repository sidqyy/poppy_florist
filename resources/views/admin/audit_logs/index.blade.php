@extends('layouts.app')

@section('title', 'Audit Log Aktivitas')
@section('page_title', 'Sistem Rekam Jejak (CCTV)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Audit Log Aktivitas</h3>
        <p class="text-gray-500 text-sm mt-1">Lacak semua perubahan data dan aktivitas penting pengguna di dalam sistem.</p>
    </div>
</div>

<div class="bg-white p-4 rounded-xl shadow-md border-2 border-gray-200 mb-6">
    <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Cari Aksi atau Nama User</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik kata kunci..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-florist-200">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-florist-200">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white font-bold rounded-lg hover:bg-gray-900 transition-colors text-sm shadow-sm"><i class="fa-solid fa-filter mr-1"></i> Saring</button>
            @if(request('q') || request('date'))
                <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition-colors text-sm">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="card-modern overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-800 text-white border-b border-gray-700">
                <tr>
                    <th class="py-4 px-4 font-medium w-48">Waktu (WIB)</th>
                    <th class="py-4 px-4 font-medium">Pelaku / Role</th>
                    <th class="py-4 px-4 font-medium">Aktivitas</th>
                    <th class="py-4 px-4 font-medium text-center">Info Data</th>
                    <th class="py-4 px-4 font-medium text-right">IP Address</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-4 font-medium text-gray-500 text-xs">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="py-4 px-4">
                        <div class="font-bold text-gray-800">{{ $log->user->name ?? 'System/Guest' }}</div>
                        @if($log->user)
                        <div class="text-xs text-gray-500 uppercase">{{ $log->user->role }}</div>
                        @endif
                    </td>
                    <td class="py-4 px-4">
                        <span class="inline-block font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded text-xs border border-gray-200">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-center">
                        @if($log->old_values || $log->new_values)
                        <button onclick="document.getElementById('modal-log-{{ $log->id }}').classList.remove('hidden')" class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded text-xs font-bold transition-colors border border-blue-200">
                            <i class="fa-solid fa-code-compare mr-1"></i> Lihat Data
                        </button>
                        @else
                        <span class="text-gray-400 text-xs italic">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-right text-xs font-mono text-gray-500" title="{{ $log->user_agent }}">
                        {{ $log->ip_address }}
                    </td>
                </tr>

                <!-- Modal Data Detail -->
                @if($log->old_values || $log->new_values)
                <div id="modal-log-{{ $log->id }}" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl">
                        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                            <h4 class="font-bold text-gray-800"><i class="fa-solid fa-magnifying-glass text-blue-500 mr-2"></i> Inspeksi Data Aktivitas</h4>
                            <button onclick="document.getElementById('modal-log-{{ $log->id }}').classList.add('hidden')" class="text-gray-400 hover:text-red-500 w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50 transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <div class="p-6 overflow-y-auto flex-1">
                            <div class="mb-4">
                                <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Aksi yang dilakukan:</span>
                                <div class="font-bold text-lg text-gray-800">{{ $log->action }}</div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div>
                                    <h5 class="text-xs font-bold text-red-500 uppercase mb-2 border-b border-red-100 pb-1"><i class="fa-solid fa-minus-circle mr-1"></i> Data Lama</h5>
                                    @if($log->old_values)
                                    <pre class="bg-red-50 p-3 rounded-lg text-xs font-mono text-red-800 overflow-x-auto border border-red-100">{{ json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                    <div class="text-gray-400 text-sm italic py-2">Tidak ada data.</div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-green-500 uppercase mb-2 border-b border-green-100 pb-1"><i class="fa-solid fa-plus-circle mr-1"></i> Data Baru</h5>
                                    @if($log->new_values)
                                    <pre class="bg-green-50 p-3 rounded-lg text-xs font-mono text-green-800 overflow-x-auto border border-green-100">{{ json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                    <div class="text-gray-400 text-sm italic py-2">Tidak ada data.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-shield-halved text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada jejak aktivitas.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100 bg-gray-50">
        {{ $logs->links() }}
    </div>
</div>
@endsection
