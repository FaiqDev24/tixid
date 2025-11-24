@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        <div class="justify-content-end d-flex">
            <a href="{{ route('admin.movies.trash') }}" class="btn btn-primary me-2">Data Sampah</a>
            <a href="{{ route('admin.movies.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.movies.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Film</h5>
        <table class="table table-bordered" id="moviesTable">
            <tr>
                <th>#</th>
                <th>Poster</th>
                <th>Judul Film</th>
                <th>Status Aktif</th>
                <th>Aksi</th>
            </tr>
            {{-- @foreach ($movies as $index => $item )
                <tr>
                    index dari 0, biar muncul xdari 1 -> +1
                    <th>{{ $index+1}}</th>
                    name, location dari fillable model cinema
                    <th><img src="{{ asset('storage/' . $item['poster']) }}" alt="" width="120"></th>
                    <th>{{ $item['title']}}</th>
                    <th>
                        @if ($item['actived'] == 1)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Non-Aktif</span>
                        @endif
                    </th>
                    <th class="d-flex">
                         event (tanda depan on) JS : menentukan js kapan dibaca
                            onclick: menjalankan js ketika btn di klik
                            <button class="btn btn-secondary me-2" onclick="showModal({{ $item }})">Detail</button>
                            <a href="{{ route('admin.movies.edit', $item['id']) }}" class="btn btn-primary me-2">Edit</a>
                            <form action="{{ route('admin.movies.delete', ['id' => $item['id']]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                            <button class="btn btn-danger me-2">Hapus</button>
                            </form>
                            @if ($item['actived'] == 1)
                                    <form action="{{ route('admin.movies.patch', $item['id']) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-warning">Non-Aktif Film</button>
                                    </form>
                            @endif
                    </th>
                </tr>
            @endforeach --}}
        </table>
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Film</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalDetailBody">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- mengisi stack --}}
@push('script')
    <script>

        $(function (){
            $('#moviesTable').DataTable({
                processing: true,
                // data untuk diproses secara serverside (controller)
                serverside: true,
                // routing menuju fungso yang memproses data untuk table
                ajax: "{{ route('admin.movies.datatables') }}",
                // urutan column (td), pastikan urutan sesuai th
                // data: 'nama' -> nama diambil dari rawColumns, atau field dari model fillable
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'poster_img', name: 'poster_img', orderable: false, searchable: false },
                    { data: 'title', name: 'title', orderable: true, searchable: true },
                    { data: 'actived_badge', name: 'actived_badge', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        function showModal(item) {
            // console.log(item)
            // pengambilan gambar di public
            let image = "{{ asset('storage/') }}" + "/" + item.poster;
            // membuat konten yang akan ditambahkan
            // backlip (diatas tab) : menulis string lebih dari 1 baris
            let content = `
                <img src="${image}" width="120" class="d-block mx-auto my-3">
                <ul>
                    <li>Judul : ${item.title}</li>
                    <li>Durasi : ${item.duration}</li>
                    <li>Genre : ${item.genre}</li>
                    <li>Sutradara : ${item.director}</li>
                    <li>Usia Minimal : <span class="badge badge-danger">${item.age_rating}</span></li>
                    <li>Sinopsis : ${item.description}</li>
                </ul>
            `;
            let modalDetailBody = document.querySelector("#modalDetailBody");
            // isi html diatas ke id="modalDetailBody"
            modalDetailBody.innerHTML = content;
            let modalDetail = document.querySelector("#modalDetail");
            // munculkan modal bootsrap
            new bootstrap.Modal(modalDetail).show();
        }
    </script>
@endpush
