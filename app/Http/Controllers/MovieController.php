<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    public function datatables()
    {
        $movies = Movie::query();
        // DataTable::of($movies) -> mengambil data dari query model movie, keseluruhan field
        // addColumn -> untuk menambahkan column yang bukan bagian dari field, biasanya digunakan untuk button atau field yang nilainya akan diolah/manipulasi
        //addIndexColumn -> mengambil index data, mulai dari 1
        return DataTables::of($movies)->addIndexColumn()->addColumn('poster_img', function($movie) {
            $url = asset('storage/' . $movie->poster);
            return '<img src="' . $url .'" width="70">';
        })->addColumn('actived_badge', function($movie) {
            if ($movie->actived) {
                return '<span class="badge badge-success">Aktif</span>';
            }
            return '<span class="badge badge-danger">Non-Aktif</span>';
        })->addColumn('action', function($movie) {
            $btnDetail = '<button class="btn btn-secondary me-2" onclick=\'showModal('. $movie . ')\'>Detail</button>';
            $btnEdit = '<a href="' . route('admin.movies.edit', $movie->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('admin.movies.delete', $movie->id) . '" method="POST">
                            ' . @csrf_field() . @method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger m-2">Hapus</button>
                        </form>';
            $btnNonAktif = '';
            if ($movie->actived) {
                $btnNonAktif = '<form action="' . route('admin.movies.nonaktif', $movie->id) . '" method="POST" class="me-2">
                                ' . @csrf_field() . @method_field('PATCH') . '
                                <button type="submit" class="btn btn-warning">Non-Aktif</button>
                            </form>';
            }

            return '<div class="d-flex justify-content-center align-items-center gap-2"> ' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
        })->rawColumns(['poster_img', 'actived_badge', 'action'])->make(true);
        // rawColumns -> mendaftarkan column yang baru dibuat pada addColumn
        // make(true) -> mengubah data php ke javascript
    }

    public function home()
    {
        // where('field', 'operator', 'value') : mencari data
        // operator : = / < / <= / > / >= / <> / !=
        // ASC : a-z, 0-9, terlama-terbaru, DESC : 9-0, z-a, terbaru ke terlama
        // limit(angka) : mengambil hanya beberapa data
        // get() : ambil hasil proses filter
        $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->limit(3)->get();
        return view('home', compact('movies'));
    }

    public function homeMovies(Request $request)
    {
        $nameMovie = $request->search_movie;
        if ($nameMovie != "") {
            $movies = Movie::where('title', 'LIKE', '%' . $nameMovie . '%')->where('actived', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->get();
        }
        return view('movies', compact('movies'));
    }

    public function movieSchedule(Request $request, $movie_id)
    {
        $sortPrice = $request->sort_price;
        if ($sortPrice) {
            $movie = Movie::where('id', $movie_id)->with(['schedules'=> function($q)
            use ($sortPrice) {
                $q->orderBy('price', $sortPrice);
            }, 'schedules.cinema'])->first();
        } else {
            // ambil data movie bersama schedule dan cinema
            // karena cinema adanya relasi dengan schedule bukan movie, jadi gunakan schedules.cinema
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
            // schedules : mengambil relasi schedules
            // schedules.cinema : ambil relasi cinema dari schedules
            // first() : karena mau ambil 1 film
        }

        $sortAlfabet = $request->sort_alfabet;
        if ($sortAlfabet == 'ASC') {
            $movie->schedules = $movie->schedules->sortBy(function($schedule) {
                return $schedule->cinema->name;
            })->values();
        } elseif ($sortAlfabet == 'DESC') {
            $movie->schedules = $movie->schedules->sortByDesc(function($schedule) {
                return $schedule->cinema->name; // diurutkan berdasarkan data ini
            })->values();
            // sortByDesc : mengurutkan DESC berdasarkan data
        }
        return view('schedule.detail', compact('movie'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required',
            'description' => 'required|min:10',
            // mimes : memastikan ekstensi (jenis file) yang diupload
            'poster' => 'required|mimes:jpeg,png,jpg,svg,webp',
        ], [
            'title.required' => 'Judul film wajib diisi',
            'duration.required' => 'Durasi film wajib diisi',
            'genre.required' => 'Genre film wajib diisi',
            'director.required' => 'Sutradara film wajib diisi',
            'age_rating.required' => 'Usia minimal film wajib diisi',
            'description.required' => 'Sinopsis film wajib diisi',
            'description.min' => 'Sinopsis film minimal 10 karakter',
            'poster.required' => 'Poster film wajib diisi',
            'poster.mimes' => 'Poster film harus berekstensi jpeg, png, jpg, svg, atau webp',
        ]);
        //  ambil file dari input : $request->file('name_input')
        $poster = $request->file('poster');
        // buat namaa baru untuk filenya
        // format file yang diharapkan : <acak>-poser.jpg
        // getClientOriginalExtension() : mengambil eksternal file yang diupload
        $namafile = Str::random(10) . "-poster." . $poster->getClientOriginalExtension();
        // simpan file ke folder storage : storeAs("namasubfolder", "namafile", visibility)
        $path = $poster->storeAs("poster", $namafile, "public");
        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'description' => $request->description,
            // poster diisi dengan hasil storeAs(), hasil penyimpanan file di storage sebelumnya
            'poster' => $path,
            'actived' => 1
        ]);
        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil membuat data baru!');
        } else {
            return redirect()->back()->with('error', 'Gagal, silakan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required',
            'description' => 'required',
            'poster' => 'mimes:jpg,jpeg,png,svg,webp'
        ], [
            'title.required' => 'Judul film wajib diisi',
            'duration.required' => 'Durasi film wajib diisi',
            'genre.required' => 'Genre film wajib diisi',
            'director.required' => 'Sutradara film wajib diisi',
            'age_rating.required' => 'Usia minimal film wajib diisi',
            'description.required' => 'Sinopsis film wajib diisi',
            'description.min' => 'Sinopsis harus diisi minimal 10 karakter',
            'poster.mimes' => 'Poster harus berupa jpg, jpeg, png, svg, atau webp'
        ]);
        // ambil data sebelumnya
        $movie = Movie::find($id);
        // jika input file poster disini
        if ($request->hasFile('poster')) {
            $filePath = storage_path('app/public/' . $movie->poster);
            // jika file ada di storage path tersebut
            if (file_exists($filePath)) {
                // hapus file lama
                unlink($filePath);
            }
            $file = $request->file('poster');
            // buat nama baru untuk file
            $fileName = 'poster-' . Str::random(10) . '.' .
                $file->getClientOriginalExtension();
            $path = $file->storeAs("poster", $fileName, "public");
        }
        $updateData = $movie->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'description' => $request->description,
            'poster' => $request->hasFile('poster') ? $path : $movie->poster,
            'actived' => 1
        ]);
        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil memperbarui data!');
        } else {
            return redirect()->back()->with('error', 'Gagal, silakan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $movie = Movie::find($id);
        $deleteData = $movie->delete();
        if ($deleteData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil menghapus data!');
        } else {
            return redirect()->back()->with('error', 'Gagal, silakan coba lagi');
        }
    }

    public function nonaktif($id)
    {
        $movie = Movie::find($id);
        $movie->actived = 0; // Nonaktifkan film
        $movie->save();

        return redirect()->route('admin.movies.index')
            ->with('success', 'Berhasil non-aktif data film!');
    }
    public function export()
    {
        // nama file yang diunduh
        $fileName = 'data-film.xlsx';
        // proses download
        return Excel::download(new MovieExport, $fileName);
    }

    public function trash()
    {
        $movieTrash = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movieTrash'));
    }

    public function restore($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->restore();
        return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $filePath = storage_path('app/public/' . $movie->poster);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $movie->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }
}

