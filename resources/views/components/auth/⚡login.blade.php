<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component
{
    public string $login_id = '';
    public string $password = '';

    public function login()
    {
        $this->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_id.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $fieldType = filter_var($this->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $value = $fieldType === 'username' ? strtolower($this->login_id) : $this->login_id;

        $user = User::where($fieldType, $value)->first();

        if (! $user) {
            $this->addError('login_id', 'Tidak ditemukan user dengan username/email tersebut.');
            return;
        }

        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Password salah, input yang benar.');
            return;
        }

        Auth::login($user);
        session()->regenerate();

        return $this->redirect('/dashboard', navigate: true);
    }
};
?>

<div class="min-h-screen flex items-center justify-center p-4">
    <flux:card class="w-full max-w-md space-y-6">
        <div>
            <flux:heading size="lg">Masuk ke Stokly</flux:heading>
            <flux:text class="mt-1">Gunakan Username atau Email yang terdaftar untuk melanjutkan.</flux:text>
        </div>

        <form wire:submit="login" class="space-y-4">
            <flux:input
                wire:model="login_id"
                type="text"
                label="Username atau Email"
                placeholder="Masukkan username atau email"
                required
            />

            <flux:input
                wire:model="password"
                type="password"
                label="Password"
                placeholder="Masukkan password"
                viewable
                required
            />

            <flux:button type="submit" variant="primary" class="w-full">Masuk</flux:button>
        </form>

        <div class="text-center text-sm space-y-2">
            <div>
                Belum punya akun?
                <flux:link href="/register" wire:navigate>Daftar gratis</flux:link>
            </div>
            <div>
                <flux:link href="/" wire:navigate class="text-xs text-zinc-500">← Kembali ke Beranda</flux:link>
            </div>
        </div>
    </flux:card>
</div>