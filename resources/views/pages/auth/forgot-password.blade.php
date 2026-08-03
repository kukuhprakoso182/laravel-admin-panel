<x-layouts.guest>
    <div class="w-full max-w-md mx-auto">
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

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-atoms.label for="email" value="Email" />
                <x-atoms.input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autofocus
                    required
                />
                <x-atoms.input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <x-atoms.button type="submit" class="w-full justify-center">
                Kirim Tautan Reset Password
            </x-atoms.button>

            <div class="text-center text-sm text-gray-500">
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                    Kembali ke halaman login
                </a>
            </div>
        </form>
    </div>
</x-layouts.guest>
