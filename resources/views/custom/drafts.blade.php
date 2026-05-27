@extends('layouts.app')

@section('title', 'Riwayat Draft Custom')
@section('page_title', 'Riwayat Draft Custom')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-500 text-sm">Daftar racikan bunga custom yang pernah Anda simpan sebelumnya.</p>
    <a href="{{ route('custom.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition-colors">
        <i class="fa-solid fa-calculator mr-2"></i> Kembali ke Kalkulator
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($drafts as $draft)
    <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">{{ str_replace('Custom Bucket - ', '', $draft->name) }}</h3>
                <p class="text-xs text-gray-400 mt-1"><i class="fa-regular fa-clock"></i> {{ $draft->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-bold">
                Rp {{ number_format($draft->total_price, 0, ',', '.') }}
            </div>
        </div>
        <div class="p-5 flex-1">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Rincian Komponen:</p>
            <ul class="space-y-2 mb-4">
                @foreach($draft->components as $comp)
                <li class="flex justify-between items-start text-sm gap-2">
                    <span class="text-gray-600 flex-1 leading-tight"><span class="font-medium text-gray-800">{{ $comp->qty }}x</span> {{ $comp->material->name }} <span class="text-xs text-gray-400 block lg:inline mt-0.5 lg:mt-0">(@ Rp {{ number_format($comp->unit_price, 0, ',', '.') }})</span></span>
                    <span class="text-gray-400 font-medium whitespace-nowrap pt-0.5">Rp {{ number_format($comp->subtotal, 0, ',', '.') }}</span>
                </li>
                @endforeach
            </ul>
            
            @php
                $descData = json_decode($draft->description, true);
                $ongkir = is_array($descData) && isset($descData['delivery_fee']) ? floatval($descData['delivery_fee']) : 0;
                $dist = is_array($descData) && isset($descData['delivery_distance']) ? floatval($descData['delivery_distance']) : 0;
            @endphp
            
            @if($ongkir > 0)
            <div class="border-t border-gray-100 pt-3 mt-3 flex justify-between items-center text-sm">
                <span class="text-gray-600 font-medium"><i class="fa-solid fa-motorcycle mr-1 text-florist-400"></i> Ongkir ({{ $dist }} km)</span>
                <span class="text-gray-400">Rp {{ number_format($ongkir, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        <div class="p-4 bg-gray-50 border-t border-gray-100">
            @php
                $waText = "*Penawaran Custom Buket*\n";
                $waText .= "Atas Nama: " . str_replace('Custom Bucket - ', '', $draft->name) . "\n\n";
                $waText .= "*Rincian Harga & Komponen:*\n";
                $subtotalBunga = 0;
                foreach($draft->components as $comp) {
                    $waText .= "- " . $comp->qty . "x " . $comp->material->name . " (@Rp " . number_format($comp->unit_price, 0, ',', '.') . ") = Rp " . number_format($comp->subtotal, 0, ',', '.') . "\n";
                    $subtotalBunga += $comp->subtotal;
                }
                
                if($ongkir > 0) {
                    $waText .= "------------------------------\n";
                    $waText .= "Subtotal Bunga: Rp " . number_format($subtotalBunga, 0, ',', '.') . "\n";
                    $waText .= "Ongkos Kirim ($dist km): Rp " . number_format($ongkir, 0, ',', '.') . "\n";
                }
                
                $waText .= "\n*TOTAL ESTIMASI:* Rp " . number_format($draft->total_price, 0, ',', '.');
            @endphp
            <textarea id="wa-text-{{ $draft->id }}" class="hidden">{{ $waText }}</textarea>
            <button onclick="copyToClipboard(this, 'wa-text-{{ $draft->id }}')" class="w-full py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold shadow-sm transition-transform active:scale-95">
                <i class="fa-brands fa-whatsapp mr-2"></i> Salin untuk WA
            </button>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center bg-white rounded-2xl border-2 border-gray-200 shadow-md">
        <i class="fa-regular fa-folder-open text-5xl text-gray-300 mb-4"></i>
        <h4 class="text-xl font-bold text-gray-500">Belum Ada Draft Tersimpan</h4>
        <p class="text-gray-400 text-sm mt-1">Anda belum pernah menyimpan draft custom bucket.</p>
    </div>
    @endforelse
</div>

<script>
function copyToClipboard(button, textareaId) {
    const text = document.getElementById(textareaId).value;
    
    const successCb = () => {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Berhasil Disalin!';
        button.classList.replace('bg-green-500', 'bg-gray-500');
        button.classList.replace('hover:bg-green-600', 'hover:bg-gray-600');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.replace('bg-gray-500', 'bg-green-500');
            button.classList.replace('hover:bg-gray-600', 'hover:bg-green-600');
        }, 2000);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(successCb).catch(err => {
            alert('Gagal menyalin teks.');
        });
    } else {
        // Fallback for HTTP (mobile IP access)
        let textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            successCb();
        } catch (err) {
            alert('Gagal menyalin teks. Browser Anda tidak mendukung.');
        }
        textArea.remove();
    }
}
</script>
@endsection
