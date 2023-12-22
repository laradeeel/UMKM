@extends('customer.layouts.customer')
@section('title_cus','meja')
@section('content_cus')

    <section id="latest-blog" class="padding-large">
        <div class="container">
            <div class="row">
                <div>
                    <br>
                    <br>
                </div>
                <div class="display-header d-flex justify-content-center pb-3">
                    <h2 class="display-7 text-dark text-uppercase">Menu Makanan</h2>
                </div>
                <div class="display-header d-flex justify-content-between pb-3">
                    <h3 class="text-dark">Pemesan: {{session()->get('nameCus')}}</h3>
                </div>
                <div class="d-flex flex-wrap">
                    <!-- menu makanan -->
                    @foreach($menus as $menu)
                    <div class="col-lg-4 col-sm-12 px-6">
                            <a href="#" class="group relative block overflow-hidden">
                                <button
                                  class="absolute end-4 top-4 z-10 -full-full bg-white p-1.5 text-gray-900 transition hover:text-gray-900/75"
                                >
                                  <span class="sr-only">Wishlist</span>
                              
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-4 w-4"
                                  >
                                    <path
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
                                    />
                                  </svg>
                                </button>
                              
                                <img
                                  src={{$menu->image_path}}
                                  alt=""
                                  class="h-64 w-full object-cover transition duration-500 group-hover:scale-105 sm:h-72"
                                />
                              
                                <div class="relative border border-gray-100 bg-white p-6">
                                  <span class="whitespace-nowrap italic bg-yellow-400 px-3 rounded-lg py-2 text-black font-medium font"> {{ $menu->name }} </span>
                              
                                  <h3 class="mt-4 text-lg font-medium font text-gray-900">{{ $menu->desc }}</h3>
                              
                                  <p class="mt-1.5 text-black text-gray-700">Rp.{{ number_format($menu->price_food, 0, ',', '.') }}</p>
                              
                                  <form class="mt-4">
                                    <a
                                      class="block w-full rounded-full bg-yellow-400 p-4 text-black text-md text-center font-extrabold transition hover:scale-105"
                                      href="{{ route('pesan.add',['meja' => session('idMeja'),'id' => $menu->id])}}">
                                      Tambahkan Kekerajang
                                    </a>
                                  </form>
                                </div>
                              </a>
                    </div>
                    @endforeach
                    <!-- end menu -->
                    
                </div>
            </div>
        </div>
    </section>
@endsection
