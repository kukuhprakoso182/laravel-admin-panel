<x-layouts.guest title="Lupa Password">
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-gray-800">Lupa Password</h1>
        <p class="text-sm text-gray-500 mt-1">
            Masukkan email akun Anda, kami akan mengirimkan tautan untuk membuat password baru.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-md px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('password.email') }}"
        class="flex flex-col gap-2"
        x-data="{ submitting: false }"
        x-on:submit="submitting = true"
    >
        @csrf

        <x-atoms.input
            type="email"
            name="email"
            label="Email"
            value="{{ old('email') }}"
            autofocus
            required
        />

        <x-atoms.button
            type="submit"
            color="teal"
            class="w-full justify-center"
            loading-when="submitting"
        >
            Kirim Tautan Reset Password
        </x-atoms.button>

        <div class="text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-teal-600 hover:underline">
                Kembali ke halaman login
            </a>
        </div>
    </form>
</x-layouts.guest>
