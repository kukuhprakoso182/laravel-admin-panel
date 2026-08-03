<x-layouts.app title="Profil Saya">

    <div class="max-w-3xl space-y-6">

        {{-- Profile Info Card --}}
        <x-molecules.card title="Informasi Profil" subtitle="Perbarui nama, email, dan foto profil Anda.">

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <x-atoms.input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        label="Nama Lengkap"
                    />
                </div>

                <div>
                    <x-atoms.input type="email" name="email" label="Email" value="{{ old('email', $user->email) }}" />
                </div>

                <div class="flex justify-end">
                    <x-atoms.button type="submit">Simpan Perubahan</x-atoms.button>
                </div>
            </form>
        </x-molecules.card>

        {{-- Change Password Card --}}
        <x-molecules.card title="Ubah Password" subtitle="Gunakan password yang kuat dan tidak dipakai di tempat lain.">

            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <x-atoms.input type="password" name="current_password" label="Password Saat Ini" value="Password Saat Ini" />
                </div>

                <div>
                    <x-atoms.input type="password" name="password" label="Password Baru"/>
                </div>

                <div>
                    <x-atoms.input type="password" name="password_confirmation" label="Konfirmasi Password Baru"/>
                </div>

                <div class="flex justify-end">
                    <x-atoms.button type="submit">Perbarui Password</x-atoms.button>
                </div>
            </form>
        </x-molecules.card>
    </div>
</x-layouts.app>
