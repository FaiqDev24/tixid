@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="justify-content-end d-flex">
            <a href="{{ route('admin.users.trash') }}" class="btn btn-primary me-2">Data Sampah</a>
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Pengguna (Admin & Staff)</h5>
        <table class="table table-bordered" id="usersTable">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            {{-- @foreach ($users  as $index => $item )
                <tr>
                    index dari 0, biar muncul xdari 1 -> +1
                    <th>{{ $index+1}}</th>
                    name, email, role dari fillable model user
                    <th>{{ $item['name']}}</th>
                    <th>{{ $item['email']}}</th>
                    <th>
                        @if ($item['role'] == 'admin')
                            <span class="badge badge-primary">Admin</span>
                        @else
                            <span class="badge badge-success">Staff</span>
                        @endif
                    </th>
                    <th class="d-flex">
                        ['id' => $item['id']] untuk mengirim id ke route
                        <a href="{{ route('admin.users.edit', ['id' => $item['id']]) }}" class="btn btn-secondary m-2">Edit</a>
                        <form action="{{ route('admin.users.delete', ['id' => $item['id']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger m-2">Hapus</button>
                        </form>
                    </th>
                </tr>
            @endforeach --}}
        </table>
    </div>
@endsection

@push('script')
    <script>
        $(function (){
            $('#usersTable').DataTable({
                processing: true,
                // data untuk diproses secara serverside (controller)
                serverside: true,
                // routing menuju fungsi yang memproses data untuk table
                ajax: "{{ route('admin.users.datatables') }}",
                // urutan column (td), pastikan urutan sesuai th
                // data: 'nama' -> nama diambil dari rawColumns, atau field dari model fillable
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true },
                    { data: 'role', name: 'role', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    // orderable : berfungsi agar data yang bernilai true bisa diurutkan
                    // searchable : berfungsi agar data yang bernilai true bisa di search
                ]
            });
        });
    </script>
@endpush

