@extends('customer.layouts.customer')
@section('title_cus','Keranjang')
@section('content_cus')

  <h1>Cek Pesanan Masuk</h1>

  @if($pesanan->count() > 0)

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Pemesan</th>
          <th>Tanggal Order</th>
          <th>Status</th>
        </tr>
      </thead>

      <tbody>
        @foreach($pesanan as $order)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $order->nama_pemesan }}</td>
            <td>{{ $order->tanggal_order }}</td>
            <td>{{ $order->status }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

  @else
    <p>Belum ada pesanan masuk</p>
  @endif

@endsection

@endsection
