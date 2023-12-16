@extends('layouts.app')
@section('body_class','login')
@section('content_login')

<div class="flex items-center justify-center h-screen">
  <div class="mx-auto max-w-screen-xl px-4 py-8 text-center">
    <div class="flex items-center justify-between">
      <div class="text-center sm:text-left">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Selamat Datang di E-UMKM</h1>
        <p class="mt-1.5 text-sm text-gray-500">Silahkan masuk untuk memulai pesanan.. 🎉</p>
      </div>
    </div>
    <div>
      <a href="/page/<?php echo rand(1, 100); ?>">
        <button class="block mx-auto rounded-full bg-cyan-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-cyan-900 focus:outline-none focus:ring" type="button">
          Masuk
        </button>
      </a>
    </div>
  </div>
</div>

@endsection
