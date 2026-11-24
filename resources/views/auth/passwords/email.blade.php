@extends('layouts.password')

@section('title', 'Lupa password')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Lupa password?</h1>
    <p class="mt-2 text-sm leading-6 text-slate-600">Masukkan email akun supplier atau distributor Anda. Kami akan mengirimkan tautan untuk mengatur ulang password.</p>

    @if (session('status'))
        <div role="status" class="mt-5 rounded-xl bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus class="w-full rounded-xl border-slate-300" />
            @error('email')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700">Kirim tautan reset</button>
    </form>
    <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-semibold text-blue-700">Kembali ke halaman masuk</a>
@endsection
