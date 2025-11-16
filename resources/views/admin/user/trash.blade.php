@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">

            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <h3 class="my-3">Data Sampah Pengguna</h3>
        @if (Session::get('success'))
            <div class="alert alert-success ">{{ Session::get('success') }}</div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Nama Bioskop</th>
                <th>Lokasi Bioskop</th>
                <th>Aksi</th>
            </tr>
            @foreach ($userTrash as $key => $item)
                <tr>
                    <td>{{ $key+1 }}</td>
                     <th>{{ $item['name']}}</th>
                    <th>{{ $item['email']}}</th>
                    <th>
                        @if ($item['role'] == 'admin')
                            <span class="badge badge-primary">Admin</span>
                        @else
                            <span class="badge badge-success">Staff</span>
                        @endif
                    </th>
                    <td class="d-flex align-items-center">
                        <form action="{{ route('admin.users.restore', $item['id']) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success ms-2">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.users.delete_permanent', $item['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger ms-2">Hapus Permanen</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
