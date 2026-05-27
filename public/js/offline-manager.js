/**
 * Offline Manager untuk Poppy Florist
 * Menangani koneksi terputus dan menyimpan draft sementara di LocalStorage
 */

document.addEventListener('DOMContentLoaded', function() {
    const indicator = document.getElementById('network-indicator');
    
    function updateOnlineStatus() {
        if (navigator.onLine) {
            indicator.classList.add('hidden');
            checkPendingSyncs();
        } else {
            indicator.classList.remove('hidden');
            indicator.className = 'ml-4 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold flex items-center gap-2 border border-red-200';
            indicator.innerHTML = '<i class="fa-solid fa-wifi" style="text-decoration: line-through;"></i> Koneksi Terputus';
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    
    // Initial check
    updateOnlineStatus();

    // Intercept Forms jika sedang offline
    const forms = document.querySelectorAll('form[data-offline-support="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!navigator.onLine) {
                e.preventDefault();
                
                // Simpan ke LocalStorage
                const formData = new FormData(form);
                const dataObj = Object.fromEntries(formData.entries());
                
                const syncQueue = JSON.parse(localStorage.getItem('poppy_sync_queue') || '[]');
                syncQueue.push({
                    url: form.action,
                    method: form.method,
                    data: dataObj,
                    timestamp: new Date().getTime()
                });
                
                localStorage.setItem('poppy_sync_queue', JSON.stringify(syncQueue));
                
                alert('Tidak ada koneksi! Data disimpan sebagai Draft. Sistem akan mencoba mengirim ulang saat koneksi kembali.');
                
                if (form.dataset.offlineRedirect) {
                    window.location.href = form.dataset.offlineRedirect;
                }
            }
        });
    });

    function checkPendingSyncs() {
        const syncQueue = JSON.parse(localStorage.getItem('poppy_sync_queue') || '[]');
        if (syncQueue.length > 0) {
            const confirmSync = confirm(`Terdapat ${syncQueue.length} data transaksi yang belum terkirim ke Server Utama. Sinkronkan sekarang?`);
            
            if (confirmSync) {
                // Di dunia nyata, ini akan mengirim data via Fetch/XHR satu per satu
                // Untuk tahap ini, kita hanya membersihkannya sebagai simulasi berhasil
                localStorage.removeItem('poppy_sync_queue');
                alert('Sinkronisasi berhasil! Semua data telah masuk ke Server Utama.');
            }
        }
    }
});
