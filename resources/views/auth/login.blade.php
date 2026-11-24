@extends('layouts.login')

@section('content')
    @if (session('status'))
        <div role="status" class="mx-auto mt-4 max-w-lg rounded-xl bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    <div id="login-container"></div>
@endsection
