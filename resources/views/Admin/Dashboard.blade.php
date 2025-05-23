<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TechFix</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100" x-data="{ openDropdown: false, openEditProfile: false, showNotif: false }">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white p-5 space-y-6">
            <h2 class="text-2xl font-bold">TechFix Admin</h2>
            <nav>
                <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 hover:bg-gray-700 rounded">Dashboard</a>
                <a href="{{ route('admin.orders') }}" class="block py-2 px-4 hover:bg-gray-700 rounded">Data Pesanan</a>
                <a href="{{ route('admin.catalog') }}" class="block py-2 px-4 hover:bg-gray-700 rounded">Data
                    Katalog</a>
                <a href="{{ route('admin.users') }}" class="block py-2 px-4 hover:bg-gray-700 rounded">Data Pengguna</a>
                {{-- <a href="{{ route('admin.pembayaran') }}" class="block py-2 px-4 hover:bg-gray-700 rounded">Pembayaran</a> --}}
            </nav>
        </aside>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
                <div class="flex items-center space-x-4 ml-auto">
                    <!-- Notifikasi -->
                    <div class="relative">
                        @php
                            $customerNotifications = $notifications->where('target_role', 'admin');
                            $unreadCount = $customerNotifications->where('is_read', false)->count();
                            $sortedNotifications = $customerNotifications->sortByDesc('created_at');
                        @endphp
                    
                        <button @click="showNotif = !showNotif" class="relative">
                            🔔
                            @if ($unreadCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>
                    
                        <div x-show="showNotif" @click.away="showNotif = false" class="absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-lg p-4 z-50">
                            <p class="font-bold border-b mb-2 pb-1">Notifikasi</p>
                    
                            @if ($unreadCount > 0)
                                <form action="{{ route('notifications.readAll') }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-sm text-blue-500">Baca Semua</button>
                                </form>
                            @endif
                    
                            <div class="notifications space-y-2 max-h-64 overflow-y-auto">
                                @forelse ($sortedNotifications as $notif)
                                    <div class="notification border-b pb-2">
                                        <strong class="text-blue-600">{{ $notif->title }}</strong>
                                        <p class="text-sm text-gray-700">{{ $notif->message }}</p>
                                        <small class="text-gray-500">{{ $notif->created_at->diffForHumans() }}</small>
                    
                                        @if (!$notif->is_read)
                                            <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="mt-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-sm text-blue-500">Mark as Read</button>
                                            </form>
                                        @else
                                            <span class="text-sm text-green-500 mt-1">Read</span>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Tidak ada notifikasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    

                    <!-- Dropdown Profile -->
                    <div class="relative">
                        <button @click="openDropdown = !openDropdown" class="flex items-center space-x-2">
                            <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : '' }}"alt="Avatar"
                                class="w-8 h-8 rounded-full">
                            <span>{{ Auth::user()->name }}</span>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="openDropdown" @click.away="openDropdown = false"
                            class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg overflow-hidden">
                            <a href="{{ route('admin.editProfile') }}" class="block px-4 py-2 hover:bg-gray-200">
                                Edit Profil
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-200">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="p-6">
                <h2 class="text-2xl font-bold mb-4">Dashboard Admin</h2>

                <div class="grid grid-cols-4 gap-4 mb-6">
                    <a class="bg-white p-5 rounded-lg shadow text-center block hover:bg-gray-100">
                        <h3 class="text-lg font-semibold text-gray-700">Total Pesanan</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ $total }}</p>
                    </a>
                    <a class="bg-white p-5 rounded-lg shadow text-center block hover:bg-gray-100">
                        <h3 class="text-lg font-semibold text-gray-700">Selesai</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ $completed }}</p>
                    </a>
                    <a class="bg-white p-5 rounded-lg shadow text-center block hover:bg-gray-100">
                        <h3 class="text-lg font-semibold text-gray-700">Menunggu Konfirmasi</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ $pending }}</p>
                    </a>
                    <a class="bg-white p-5 rounded-lg shadow text-center block hover:bg-gray-100">
                        <h3 class="text-lg font-semibold text-gray-700">Ditolak</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ $rejected }}</p>
                    </a>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
