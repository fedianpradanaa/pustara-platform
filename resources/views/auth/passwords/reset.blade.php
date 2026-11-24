@extends('layouts.password')

@section('title', 'Atur ulang password')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Atur ulang password</h1>
    <p class="mt-2 text-sm leading-6 text-slate-600">Buat password baru minimal 8 karakter untuk akun supplier atau distributor Anda.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}" />
        <div>
            <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" class="w-full rounded-xl border-slate-300" />
            @error('email')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password" class="mb-1 block text-sm font-semibold">Password baru</label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border-slate-300" />
            @error('password')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password-confirmation" class="mb-1 block text-sm font-semibold">Konfirmasi password baru</label>
            <input id="password-confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border-slate-300" />
        </div>
        <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700">Simpan password baru</button>
    </form>
    <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-semibold text-blue-700">Kembali ke halaman masuk</a>
@endsection
