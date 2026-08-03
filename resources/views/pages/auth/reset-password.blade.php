<x-layouts.guest>
    <div class="w-full max-w-md mx-auto">
        <div class="mb-6 text-center">
            <h1 class="text-xl font-semibold text-gray-800">Buat Password Baru</h1>
            <p class="text-sm text-gray-500 mt-1">
                Silakan buat password baru untuk akun Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <x-atoms.label for="email" value="Email" />
                <x-atoms.input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $email) }}"
                    autofocus
                    required
                />
                <x-atoms.input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <x-atoms.label for="password" value="Password Baru" />
                <x-atoms.input
                    id="password"
                    name="password"
                    type="password"
                    required
                />
                <x-atoms.input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <x-atoms.label for="password_confirmation" value="Konfirmasi Password" />
                <x-atoms.input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                />
                <x-atoms.input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <x-atoms.button type="submit" class="w-full justify-center">
                Reset Password
            </x-atoms.button>
        </form>
    </div>
</x-layouts.guest>
