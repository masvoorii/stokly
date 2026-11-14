<x-layouts::app title="Stokly — Kelola Stok & Inventaris Usaha Tanpa Ribet">
    <div class="min-h-screen flex flex-col justify-between">
        <!-- Navbar -->
        <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur sticky top-0 z-30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <a href="/" wire:navigate class="flex items-center gap-2.5 font-bold text-lg tracking-tight">
                    <span class="w-8 h-8 rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 flex items-center justify-center text-sm font-black">
                        S
                    </span>
                    <span>Stokly</span>
                </a>

                <div class="flex items-center gap-2">
                    @auth
                        <flux:button href="/dashboard" variant="primary" size="sm" wire:navigate>
                            Buka Workspace
                        </flux:button>
                    @else
                        <flux:button href="/login" variant="ghost" size="sm" wire:navigate>
                            Masuk
                        </flux:button>
                        <flux:button href="/register" variant="primary" size="sm" wire:navigate>
                            Daftar Gratis
                        </flux:button>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="max-w-5xl mx-auto px-4 sm:px-6 py-16 sm:py-24 text-center space-y-10">
            <div class="space-y-4 max-w-2xl mx-auto">
                <flux:badge size="sm">Manajemen Inventaris Modern</flux:badge>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                    Pantau Stok Barang Lebih Cepat, Rapi, dan Akurat
                </h1>
                <p class="text-zinc-600 dark:text-zinc-400 text-base sm:text-lg leading-relaxed">
                    Catat barang masuk, perbarui harga, dan amankan riwayat stok usaha Anda dalam satu ruang kerja yang ringan serta mudah diakses dari perangkat apa pun.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                @auth
                    <flux:button href="/dashboard" variant="primary" wire:navigate>
                        Lanjut ke Workspace ({{ auth()->user()->name }})
                    </flux:button>
                @else
                    <flux:button href="/register" variant="primary" wire:navigate>
                        Mulai Sekarang — Gratis
                    </flux:button>
                    <flux:button href="/login" variant="ghost" wire:navigate>
                        Sudah Punya Akun
                    </flux:button>
                @endauth
            </div>

            <!-- 3 Keunggulan Produk -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-8 text-left">
                <flux:card class="space-y-2">
                    <flux:heading size="md">Pencatatan Real-Time</flux:heading>
                    <flux:text>
                        Tambah, ubah, dan cari ribuan item barang seketika tanpa jeda memuat ulang halaman.
                    </flux:text>
                </flux:card>

                <flux:card class="space-y-2">
                    <flux:heading size="md">Pemulihan Data Aman</flux:heading>
                    <flux:text>
                        Barang yang terhapus tersimpan aman di Arsip Sampah sehingga dapat dipulihkan kapan saja.
                    </flux:text>
                </flux:card>

                <flux:card class="space-y-2">
                    <flux:heading size="md">Akses Fleksibel</flux:heading>
                    <flux:text>
                        Tampilan menyesuaikan layar ponsel maupun desktop agar stok tetap terpantau di mana saja.
                    </flux:text>
                </flux:card>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-zinc-200 dark:border-zinc-800 py-6 text-center text-xs text-zinc-500">
            &copy; {{ date('Y') }} Stokly. Hak cipta dilindungi.
        </footer>
    </div>
</x-layouts::app>