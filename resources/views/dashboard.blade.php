<x-layouts::app title="Dashboard">
    <div class="max-w-4xl mx-auto p-6 space-y-4">
        <flux:card class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Selamat Datang, {{ auth()->user()->name }}!</flux:heading>
                <flux:text>Login mandiri dan Hash password berhasil.</flux:text>
            </div>

            <form method="POST" action="/logout">
                @csrf
                <flux:button type="submit" variant="danger">Logout</flux:button>
            </form>
        </flux:card>
    </div>
</x-layouts::app>