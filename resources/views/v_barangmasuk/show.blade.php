@extends('layout.adm-main1')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow rounded">
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Tanggal Masuk</td>
                                <td>{{ $barangMasuk->tgl_masuk }}</td>
                            </tr>
                            <tr>
                                <td>Qty Masuk</td>
                                <td>{{ $barangMasuk->qty_masuk }}</td>
                            </tr>
                            <tr>
                                <td>Barang</td>
                                <td>{{ $barangMasuk->barang->merk . ' ' . $barangMasuk->barang->seri }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <a href="{{ route('barangmasuk.index') }}" class="btn btn-md btn-primary mb-3">Back</a>
            </div>
        </div>
    </div>
@endsection
