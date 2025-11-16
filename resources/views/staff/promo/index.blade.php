@extends('templates.app')

@section('content')
    <div class="container my-4">
        <h4>Daftar Promo</h4>
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('staff.promos.trash') }}" class="btn btn-primary me-2">Data Sampah</a>
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <table class="table table-bordered" id="promoTables">
            <tr>
                <th>#</th>
                <th>Kode Promo</th>
                <th>Total Potongan</th>
                <th>Aksi</th>
            </tr>
            {{-- @foreach ($promos as $key => $index)
                <tr>
                    <th>{{ $key+1 }}</th>
                    <th>{{ $index['promo_code'] }}</th>
                    <th>
                        @if ($index['type'] == 'percent')
                            {{ $index['discount'] }}%
                        @else
                            Rp {{ number_format($index['discount'], 0, ',', '.') }}
                        @endif
                    </th>
                    <th>
                        <a href="{{ route('staff.promos.edit', $index['id']) }}" class="btn btn-secondary m-2">Edit</a>
                        <form action="{{ route('staff.promos.delete', $index['id']) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger m-2">
                                Hapus
                            </button>
                        </form>
                    </th>
                </tr>
            @endforeach --}}
        </table>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('#promoTables').DataTable({
                processing: true,
                serverside: true,
                ajax: "{{ route('staff.promos.datatables') }}",
                columns: [{
                    data: 'DT_RowIndex',
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                }, {
                    data: 'promo_code',
                    name: "promo_code",
                    orderable: true,
                    searchable: true
                }, {
                    data: 'discount',
                    name: "discount",
                    orderable: false,
                    searchable: false
                }, {
                    data: 'action',
                    name: "action",
                    orderable: false,
                    searchable: false
                }]
            });
        });
    </script>
@endpush
