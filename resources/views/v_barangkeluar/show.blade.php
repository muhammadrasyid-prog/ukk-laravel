@extends('layout.adm-main1')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow rounded">
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Tanggal Keluar</td>
                                <td>{{ $barangKeluar->tgl_keluar }}</td>
                            </tr>
                            <tr>
                                <td>Qty Keluar</td>
                                <td>{{ $barangKeluar->qty_keluar }}</td>
                            </tr>
                            <tr>
                                <td>Barang</td>
                                <td>{{ $barangKeluar->barang->merk . ' ' . $barangKeluar->barang->seri }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <a href="{{ route('barangkeluar.index') }}" class="btn btn-md btn-primary mb-3">Back</a>
            </div>
        </div>
    </div>
@endsection
