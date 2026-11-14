<?php

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $category = '';
    public int|string $stock = '';
    public int|string $price = '';

    public ?int $editingId = null;
    public bool $showTrash = false;
    public string $search = '';

    private function isAdmin(): bool
    {
        return (bool) Auth::user()->is_admin;
    }

    private function productQuery()
    {
        $query = Product::query();

        if (! $this->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    private function clearProductCache(): void
    {
        $userId = Auth::id();
        Cache::forget("products_active_list_{$userId}");
        Cache::forget("products_stats_{$userId}");
        Cache::forget('products_active_list_admin');
        Cache::forget('products_stats_admin');
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|min:2|max:255',
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
        ]);

        if ($this->editingId) {
            $this->productQuery()
                ->findOrFail($this->editingId)
                ->update($validated);
            session()->flash('message', 'Data barang berhasil diperbarui.');
        } else {
            Product::create(array_merge($validated, [
                'user_id' => Auth::id(),
            ]));
            session()->flash('message', 'Barang baru berhasil ditambahkan.');
        }

        $this->clearProductCache();
        $this->resetForm();
    }

    public function edit(int $id)
    {
        $product = $this->productQuery()->findOrFail($id);
        $this->editingId = $product->id;
        $this->name = $product->name;
        $this->category = $product->category;
        $this->stock = $product->stock;
        $this->price = $product->price;
    }

    public function resetForm()
    {
        $this->reset(['name', 'category', 'stock', 'price', 'editingId']);
        $this->resetValidation();
    }

    public function softDelete(int $id)
    {
        $this->productQuery()->findOrFail($id)->delete();
        $this->clearProductCache();
        session()->flash('message', 'Barang dipindahkan ke arsip sampah.');
    }

    public function restore(int $id)
    {
        $this->productQuery()->onlyTrashed()->findOrFail($id)->restore();
        $this->clearProductCache();
        session()->flash('message', 'Barang berhasil dipulihkan.');
    }

    public function forceDelete(int $id)
    {
        $this->productQuery()->onlyTrashed()->findOrFail($id)->forceDelete();
        $this->clearProductCache();
        session()->flash('message', 'Barang dihapus permanen.');
    }

    public function with(): array
    {
        $cacheKeySuffix = $this->isAdmin() ? 'admin' : Auth::id();

        $stats = Cache::remember("products_stats_{$cacheKeySuffix}", 300, function () {
            return [
                'total_items' => $this->productQuery()->count(),
                'total_stock' => $this->productQuery()->sum('stock'),
                'trashed_count' => $this->productQuery()->onlyTrashed()->count(),
            ];
        });

        if ($this->showTrash) {
            $products = $this->productQuery()
                ->with('user:id,name,username')
                ->onlyTrashed()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest('deleted_at')
                ->get()
                ->toArray();
        } else {
            if (empty($this->search)) {
                $products = Cache::remember("products_active_list_{$cacheKeySuffix}", 300, function () {
                    return $this->productQuery()
                        ->with('user:id,name,username')
                        ->latest()
                        ->get()
                        ->toArray();
                });
            } else {
                $products = $this->productQuery()
                    ->with('user:id,name,username')
                    ->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('category', 'like', "%{$this->search}%");
                    })
                    ->latest()
                    ->get()
                    ->toArray();
            }
        }

        return [
            'items' => $products,
            'stats' => $stats,
            'isAdmin' => $this->isAdmin(),
        ];
    }
};
?>

<div class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6">
    <!-- Top Bar Aplikasi -->
    <flux:card class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/" wire:navigate class="w-9 h-9 rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 flex items-center justify-center font-black">
                S
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="lg">Stokly</flux:heading>
                    @if ($isAdmin)
                        <flux:badge size="sm">Admin</flux:badge>
                    @endif
                </div>
                <flux:text class="text-xs">
                    {{ $isAdmin ? 'Monitoring Seluruh Inventaris Pengguna' : 'Ruang Kerja Inventaris' }}
                </flux:text>
            </div>
        </div>

        <!-- Dropdown Akun Pengguna -->
        <div x-data="{ openMenu: false }" class="relative">
            <flux:button type="button" variant="ghost" @click="openMenu = !openMenu">
                {{ auth()->user()->name }} ▾
            </flux:button>

            <div x-show="openMenu"
                 @click.outside="openMenu = false"
                 x-transition
                 class="absolute right-0 mt-2 w-56 p-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-lg z-20 space-y-3">
                <div class="px-1 border-b border-zinc-200 dark:border-zinc-700 pb-2">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-zinc-500 truncate">{{ '@' . auth()->user()->username }}</p>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <flux:button type="submit" variant="danger" size="sm" class="w-full">
                        Keluar Akun
                    </flux:button>
                </form>
            </div>
        </div>
    </flux:card>

    <!-- Notifikasi Toast -->
    @if (session()->has('message'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3500)"
             x-show="show"
             x-transition
             class="p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 flex items-center justify-between text-sm">
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="font-bold px-2">&times;</button>
        </div>
    @endif

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <flux:card>
            <flux:text>Total Jenis Barang</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $stats['total_items'] }} Item</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Total Stok Tersedia</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $stats['total_stock'] }} Unit</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Arsip Sampah</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $stats['trashed_count'] }} Item</flux:heading>
        </flux:card>
    </div>

    <!-- Layout Utama: Form & Tabel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Form Tambah / Edit -->
        <flux:card class="space-y-4">
            <flux:heading size="md">
                {{ $editingId ? 'Edit Data Barang' : 'Tambah Barang Baru' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:input wire:model="name" label="Nama Barang" placeholder="Contoh: Mouse Wireless" required />
                <flux:input wire:model="category" label="Kategori" placeholder="Contoh: Elektronik / Aksesoris" required />
                <flux:input wire:model="stock" type="number" label="Jumlah Stok" placeholder="0" required />
                <flux:input wire:model="price" type="number" label="Harga Satuan (Rp)" placeholder="0" required />

                <div class="flex gap-2 pt-2">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Barang' }}
                    </flux:button>

                    @if ($editingId)
                        <flux:button type="button" variant="ghost" wire:click="resetForm">
                            Batal
                        </flux:button>
                    @endif
                </div>
            </form>
        </flux:card>

        <!-- Daftar Data & Arsip Sampah -->
        <flux:card class="lg:col-span-2 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex gap-2">
                    <flux:button type="button"
                        :variant="!$showTrash ? 'primary' : 'ghost'"
                        wire:click="$set('showTrash', false)">
                        Daftar Barang ({{ $stats['total_items'] }})
                    </flux:button>
                    <flux:button type="button"
                        :variant="$showTrash ? 'danger' : 'ghost'"
                        wire:click="$set('showTrash', true)">
                        Sampah ({{ $stats['trashed_count'] }})
                    </flux:button>
                </div>

                <div class="w-full sm:w-60">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari barang..." />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500">
                            <th class="py-3 px-2">Nama Barang</th>
                            <th class="py-3 px-2">Kategori</th>
                            @if ($isAdmin)
                                <th class="py-3 px-2">Pemilik</th>
                            @endif
                            <th class="py-3 px-2">Stok</th>
                            <th class="py-3 px-2">Harga</th>
                            <th class="py-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($items as $item)
                            <tr wire:key="product-{{ $item['id'] }}">
                                <td class="py-3 px-2 font-medium">{{ $item['name'] }}</td>
                                <td class="py-3 px-2">
                                    <flux:badge size="sm">{{ $item['category'] }}</flux:badge>
                                </td>
                                @if ($isAdmin)
                                    <td class="py-3 px-2 text-xs text-zinc-500">
                                        {{ $item['user']['name'] ?? '-' }}
                                    </td>
                                @endif
                                <td class="py-3 px-2">{{ $item['stock'] }}</td>
                                <td class="py-3 px-2">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2 text-right space-x-1 whitespace-nowrap">
                                    @if (!$showTrash)
                                        <flux:button size="sm" wire:click="edit({{ $item['id'] }})">
                                            Edit
                                        </flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="softDelete({{ $item['id'] }})">
                                            Hapus
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" variant="primary" wire:click="restore({{ $item['id'] }})">
                                            Pulihkan
                                        </flux:button>
                                        <flux:button size="sm" variant="danger"
                                            wire:click="forceDelete({{ $item['id'] }})"
                                            wire:confirm="Hapus barang ini secara permanen?">
                                            Hapus Permanen
                                        </flux:button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 6 : 5 }}" class="py-8 text-center text-zinc-500">
                                    {{ $showTrash ? 'Arsip sampah kosong.' : 'Belum ada data barang.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</div>