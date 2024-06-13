@extends('layout.adm-main1')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-6 bg-light text-left">
                <a href="{{ route('barangkeluar.create') }}" class="btn btn-md btn-success btn-sm pull-right">TAMBAH BARANG MASUK</a>
                </div>
                <div class="col-md-6 bg-light text-right">
                    
                    <form action="/barangkeluar" method="GET"
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                     @endif

            @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                     @endif

                     @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TANGGAL KELUAR</th>
                            <th>QTY KELUAR</th>
                            <th>BARANG</th>
                            <th style="width: 15%">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangKeluar as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->tgl_keluar }}</td>
                                <td>{{ $row->qty_keluar }}</td>
                                <td>{{ $row->barang_id }} - {{ $row->barang->merk }}</td>
                                <td class="text-center">
                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('barangkeluar.destroy', $row->id) }}" method="POST">
                                        <a href="{{ route('barangkeluar.show', $row->id) }}" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('barangkeluar.edit', $row->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="alert">
                                        Data Barang Keluar belum tersedia
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- {{ $barangKeluar->links() }} --}}
            </div>
        </div>
    </div>
@endsection
