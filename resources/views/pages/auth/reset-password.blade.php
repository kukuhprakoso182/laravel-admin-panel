<x-layouts.guest title="Buat Password Baru">
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-gray-800">Buat Password Baru</h1>
        <p class="text-sm text-gray-500 mt-1">
            Silakan buat password baru untuk akun Anda.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('password.update') }}"
        class="flex flex-col gap-2"
        x-data="{ submitting: false }"
        x-on:submit="submitting = true"
    >
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <x-atoms.input
            type="email"
            name="email"
            label="Email"
            value="{{ old('email', $email) }}"
            autofocus
            required
        />

        <x-atoms.input
            type="password"
            name="password"
            label="Password Baru"
            placeholder="Minimal 8 karakter"
            required
        />

        <x-atoms.input
            type="password"
            name="password_confirmation"
            label="Konfirmasi Password"
            required
        />

        <x-atoms.button
            type="submit"
            color="teal"
            class="w-full justify-center"
            loading-when="submitting"
        >
            Reset Password
        </x-atoms.button>
    </form>
</x-layouts.guest>
