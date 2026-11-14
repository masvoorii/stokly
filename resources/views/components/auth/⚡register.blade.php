<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'username' => 'required|string|min:3|max:50|alpha_dash|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'username' => strtolower($this->username),
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        session()->regenerate();

        return $this->redirect('/dashboard', navigate: true);
    }
};
?>

<div class="min-h-screen flex items-center justify-center p-4">
    <flux:card class="w-full max-w-md space-y-6">
        <div>
            <flux:heading size="lg">Buat Akun Stokly</flux:heading>
            <flux:text class="mt-1">Mulai kelola inventaris dan stok barang Anda secara terpusat.</flux:text>
        </div>

        <form wire:submit="register" class="space-y-4">
            <flux:input wire:model="name" label="Nama Lengkap" placeholder="Contoh: Budi Santoso" required />
            <flux:input wire:model="username" label="Username" placeholder="Contoh: budisantoso (tanpa spasi)" required />
            <flux:input wire:model="email" type="email" label="Email" placeholder="nama@domain.com" required />
            <flux:input wire:model="password" type="password" label="Password" placeholder="Minimal 6 karakter" viewable required />
            <flux:input wire:model="password_confirmation" type="password" label="Konfirmasi Password" placeholder="Ulangi password" viewable required />

            <flux:button type="submit" variant="primary" class="w-full">Daftar Sekarang</flux:button>
        </form>

        <div class="text-center text-sm space-y-2">
            <div>
                Sudah punya akun?
                <flux:link href="/login" wire:navigate>Masuk di sini</flux:link>
            </div>
            <div>
                <flux:link href="/" wire:navigate class="text-xs text-zinc-500">← Kembali ke Beranda</flux:link>
            </div>
        </div>
    </flux:card>
</div>