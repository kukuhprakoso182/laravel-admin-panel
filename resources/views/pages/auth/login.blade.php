<x-layouts.guest title="Login">
    <form
        method="POST"
        action="{{ route('login') }}"
        class="flex flex-col gap-2"
        x-data="{ submitting: false }"
        x-on:submit="submitting = true"
    >
        @csrf
        <x-atoms.input type="email" name="email" label="Email" />
        <x-atoms.input type="password" name="password" label="Password" />

        <x-atoms.button
            type="submit"
            color="teal"
            loading-when="submitting"
        >
            Login
        </x-atoms.button>
    </form>
</x-layouts.guest>
