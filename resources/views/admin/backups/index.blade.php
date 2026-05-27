@extends('layouts.app')

@section('title', 'Manajemen Backup Database')
@section('page_title', 'Keamanan & Backup Data')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Backup Database</h3>
        <p class="text-gray-500 text-sm mt-1">Cetak file cadangan untuk mencegah kehilangan data atau pindahkan ke server lain.</p>
    </div>
    <div>
        <form action="{{ route('admin.backups.run') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-black text-white font-bold rounded-lg shadow-sm transition-colors flex items-center" onclick="return confirm('Proses backup akan memakan waktu beberapa saat tergantung ukuran database. Lanjutkan?')">
                <i class="fa-solid fa-hard-drive mr-2"></i> Backup Sekarang
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 flex items-center gap-3 shadow-sm">
    <i class="fa-solid fa-check-circle text-green-500 text-xl"></i>
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-800"><i class="fa-solid fa-clock-rotate-left text-blue-500 mr-2"></i> Riwayat Backup</h4>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-gray-400 border-b border-gray-100 text-xs uppercase">
                        <tr>
                            <th class="py-3 px-5 font-bold">Nama File</th>
                            <th class="py-3 px-5 font-bold text-center">Ukuran</th>
                            <th class="py-3 px-5 font-bold">Tanggal Dibuat</th>
                            <th class="py-3 px-5 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 divide-y divide-gray-50">
                        @forelse($backups as $backup)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5 font-mono text-gray-700 font-medium text-xs">{{ $backup['name'] }}</td>
                            <td class="py-3 px-5 text-center font-bold text-gray-500">{{ $backup['size'] }}</td>
                            <td class="py-3 px-5">{{ $backup['last_modified']->format('d M Y - H:i') }} WIB</td>
                            <td class="py-3 px-5 text-right flex justify-end gap-2">
                                <a href="{{ route('admin.backups.download', $backup['name']) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors" title="Download SQL">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <form action="{{ route('admin.backups.destroy', $backup['name']) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" onclick="return confirm('Hapus file backup ini permanen?')" title="Hapus Permanen">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-box-open text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada file backup database.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Panel Sidebar -->
    <div class="space-y-6">
        <!-- Info Lokasi -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
            <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Informasi Sistem</h4>
            <div class="mb-4">
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Lokasi Folder Backup</span>
                <code class="block bg-gray-100 text-gray-600 p-2 rounded text-xs break-all border border-gray-200">
                    {{ storage_path('app/backups') }}
                </code>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Jadwal Auto-Backup</span>
                <p class="font-medium text-gray-800 text-sm"><i class="fa-solid fa-robot text-green-500 mr-1"></i> Setiap Hari pukul 21:30 WITA</p>
                <p class="text-xs text-gray-400 mt-1 italic">*Memerlukan konfigurasi Windows Task Scheduler / CronJob</p>
            </div>
        </div>

        <!-- Restore Data -->
        <div class="bg-red-50 rounded-2xl shadow-sm border border-red-200 p-6 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-10">
                <i class="fa-solid fa-triangle-exclamation text-9xl text-red-500"></i>
            </div>
            
            <h4 class="font-bold text-red-700 mb-4 border-b border-red-200 pb-2 relative z-10"><i class="fa-solid fa-rotate-left mr-2"></i> Restore Database</h4>
            
            <p class="text-xs text-red-600 mb-4 relative z-10 leading-relaxed font-medium">
                Peringatan: Mengembalikan data (Restore) akan <strong>MENGHAPUS / MENIMPA</strong> seluruh data yang ada saat ini. Lakukan hanya dalam keadaan darurat atau migrasi sistem.
            </p>

            <form action="{{ route('admin.backups.restore') }}" method="POST" enctype="multipart/form-data" class="relative z-10">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-red-700 mb-2">Upload File SQL</label>
                    <input type="file" name="backup_file" accept=".sql" required class="w-full px-3 py-2 border border-red-300 bg-white rounded-lg text-sm text-gray-600 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:bg-red-100 file:text-red-700 hover:file:bg-red-200 cursor-pointer">
                </div>
                <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm" onclick="return confirm('PERINGATAN BAHAYA!\n\nSeluruh data transaksi dan stok saat ini akan TERHAPUS dan digantikan dengan data dari file backup yang Anda upload.\n\nApakah Anda benar-benar yakin ingin melanjutkan RESTORE DATABASE?')">
                    Eksekusi Restore
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
