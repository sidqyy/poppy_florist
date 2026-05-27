<header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 flex-shrink-0 border-b border-florist-100">
    <div class="flex items-center gap-4">
        <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-florist-500 transition-colors">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <!-- Dynamic Page Title based on yield if needed -->
        <h2 class="text-xl font-semibold text-gray-800 hidden md:block">@yield('page_title', 'Dashboard')</h2>
        
        <!-- Network Indicator -->
        <div id="network-indicator" class="hidden ml-4 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold items-center gap-1 border border-green-200">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span>Online</span>
        </div>
    </div>

    <div class="flex-1 max-w-xl px-4 md:px-8">
        <form action="{{ route('orders.index') }}" method="GET" class="relative group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-search text-gray-400 group-focus-within:text-florist-500 transition-colors"></i>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pesanan (No Order, Nama, WA)..." 
                class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-full focus:ring-2 focus:ring-florist-200 focus:border-florist-400 block pl-10 p-2 transition-all outline-none shadow-inner" autocomplete="off">
            <button type="submit" class="hidden">Search</button>
        </form>
    </div>

    <div class="flex items-center gap-4">
        <!-- Notification Dropdown Container -->
        <div class="relative" id="notification-dropdown-container">
            <button id="notification-btn" data-hash="{{ $notifHash ?? '' }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:bg-florist-50 hover:text-florist-500 transition-colors relative">
                <i class="fa-regular fa-bell text-lg"></i>
                @if(isset($unreadCount) && $unreadCount > 0)
                <span id="notif-badge" class="absolute top-2 right-2 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                </span>
                @endif
            </button>
            
            <!-- Dropdown Menu -->
            <div id="notification-menu" class="absolute right-0 md:right-auto md:-left-32 mt-2 w-80 bg-white rounded-xl shadow-lg border-2 border-gray-200 shadow-md hidden z-50 transform origin-top-right transition-all">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Notifikasi</h3>
                    @if(isset($unreadCount) && $unreadCount > 0)
                    <span id="notif-new-text" class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }} Baru</span>
                    @endif
                </div>
                
                <div class="max-h-[300px] overflow-y-auto">
                    @if(isset($notifications) && count($notifications) > 0)
                        @foreach($notifications as $notif)
                        <a href="{{ $notif['link'] }}" class="block p-4 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-full {{ $notif['bg'] }} {{ $notif['color'] }} flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid {{ $notif['icon'] }}"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-800">{{ $notif['title'] }}</h4>
                                    <p class="text-xs text-gray-500 mt-1 leading-tight">{{ $notif['message'] }}</p>
                                    <span class="text-[10px] text-gray-400 mt-2 block">{{ $notif['time'] }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="p-6 text-center text-gray-400">
                            <i class="fa-regular fa-bell-slash text-3xl mb-2 opacity-50"></i>
                            <p class="text-sm">Belum ada notifikasi baru.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3 border-l border-gray-200 pl-4">
            <div class="w-10 h-10 rounded-full bg-florist-200 flex items-center justify-center text-florist-600 font-bold">
                {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'U' }}
            </div>
            <div class="hidden md:block text-sm">
                <p class="font-semibold text-gray-700">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ auth()->check() ? auth()->user()->role : 'Role' }}</p>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notification-btn');
    const notifMenu = document.getElementById('notification-menu');
    const notifBadge = document.getElementById('notif-badge');
    const notifNewText = document.getElementById('notif-new-text');
    
    if(notifBtn && notifMenu) {
        const currentHash = notifBtn.getAttribute('data-hash');
        const seenHash = localStorage.getItem('seen_notif_hash');
        
        // Hide badge if this exact notification state has been seen
        if (seenHash === currentHash && currentHash !== '') {
            if (notifBadge) notifBadge.style.display = 'none';
            if (notifNewText) notifNewText.style.display = 'none';
        }
        
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifMenu.classList.toggle('hidden');
            
            // Mark as read when clicked
            if (currentHash) {
                localStorage.setItem('seen_notif_hash', currentHash);
                if (notifBadge) notifBadge.style.display = 'none';
                if (notifNewText) notifNewText.style.display = 'none';
            }
        });
        
        document.addEventListener('click', (e) => {
            if(!notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.add('hidden');
            }
        });
    }
});
</script>
