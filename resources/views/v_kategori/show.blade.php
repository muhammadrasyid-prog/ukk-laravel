@extends('layout.adm-main1')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Show Kategori</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td>ID</td>
                            <td>{{ $rsetKategori->id }}</td>
                        </tr>
                        <tr>
                            <td>DESKRIPSI</td>
                            <td>{{ $rsetKategori->deskripsi }}</td>
                        </tr>
                        <tr>
                            <td>KATEGORI</td>
                            <td>{{ $rsetKategori->kategori }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12  text-center">
            <a href="{{ route('kategori.index') }}" class="btn btn-md btn-primary mb-3">Back</a>
        </div>
    </div>
</div>
@endsection