<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Buat akun pembeli</h1>
        <p class="mt-1 text-sm text-gray-600">Simpan dan pantau semua order dari satu tempat.</p>
    </div>

    <form method="POST" action="{{ route('account.register.store') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama lengkap')" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Nomor WhatsApp')" />
            <x-text-input id="phone" class="mt-1 block w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="0812..." />
            <p class="mt-1 text-xs text-gray-500">Digunakan untuk verifikasi order guest.</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi password')" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">Sudah punya akun?</a>
            <x-primary-button>{{ __('Daftar') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
