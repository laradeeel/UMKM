@extends('customer.layouts.customer')
@section('title_cus','pesan')
@section('content_cus')
    <section id="latest-blog" class="padding-large">
        <div class="container">
            <br>
            <br>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="row justify-content-center text-md-center">
                        <br>
                        <h3>Selamat datang di Dand'ys Food</h3>
                        <h4>Beritahu kami namamu</h4>
                        <br>
                    </div>
                    <form method="POST" action="{{ route('pesan.setupSession') }}">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{ $idMeja }}">
                        <br><br>
                        <div class="mb-3">
                            <label for="namaCus" class="form-label">Nama Pemesan</label>
                            <input type="text"class="form-control"id="namaCus" name="namaCus"/>
                            <div id="namaCusHelp" class="form-text">
                                Isikan nama dengan benar untuk kelancaran dalam transaksi.
                            </div>
                        </div>
                        <button id="btn-login" type="submit" class="btn btn-primary">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
