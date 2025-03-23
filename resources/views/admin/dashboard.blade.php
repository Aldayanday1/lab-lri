@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h2>Dashboard Admin - Lembaga Riset Internal UMY</h2>
            <p>Selamat datang, {{ Auth::user()->name }}!</p>
            {{-- <a href="{{ route('admin.verify-users') }}" class="btn btn-primary">Verifikasi Pengguna</a> --}}
        </div>
    </div>
@endsection
