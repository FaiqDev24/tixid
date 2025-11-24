<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Ticket;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScheduleExport;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();

        // with() memanggil detail relasi, tidak hanya angka id nnya
        // ii with() dari function relasi di model
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return view('staff.schedule.index', compact('cinemas', 'movies', 'schedules'));
    }

    public function DataTables()
    {
        $schedules = Schedule::with(['cinema', 'movie']);
        return DataTables::of($schedules)->addIndexColumn()
            ->addColumn('cinema_id', function ($schedule) {
                return $schedule->cinema->name ?? '-';
            })
            ->addColumn('movie_id', function ($schedule) {
                return $schedule->movie->title ?? '-';
            })
            ->addColumn('price', function ($schedule) {
                return 'Rp ' . number_format($schedule->price, 0, ',', '.');
            })
            ->addColumn('hours', function ($schedule) {
                $hours = $schedule->hours ?? [];
                sort($hours);
                return implode(', ', $hours);
            })
            ->addColumn('action', function ($schedule) {
                $btnEdit = '<a href="' . route('staff.schedules.edit', $schedule->id) . '" class="btn btn-primary me-2">Edit</a>';
                $btnDelete = '<form action="' . route('staff.schedules.delete', $schedule->id) . '" method="POST">
                            ' . @csrf_field() . @method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger m-2">Hapus</button>
                        </form>';
                return '<div class="d-flex justify-content-center align-items-center gap-2"> ' . $btnEdit . $btnDelete . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            // karna hours array,, yang divalidasi isi itemnya menggunakan (.*)
            // date_format : bentuk item arraynya berupa format waktu H:i
            'hours.*' => 'required|date_format:H:i',
        ], [
            'cinema_id.required' => 'Bioskop harus dipilih',
            'movie_id.required' => 'Film harus dipilih',
            'price.required' => 'harga harus diisi',
            'price.numeric' => 'harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang diisi minimal 1 data',
            'hours.*.date_format' => 'Jam tayang diisi dengan jam:menit',
        ]);

        //cek apakah data bioskop dan film yang dipilih sudah ada, kalo ada ambil jamnya
        $hours = Schedule::where('cinema_id', $request->cinema_id)->where('movie_id', $request->movie_id)->value('hours');
        // value('hours') : dari schedue cuma diambil bagian hours
        // jika belum ada data bioskop dan f ilm, hours akan NULL ubah menjadi []
        $hoursBefore = $hours ?? [];
        // gabunngkan hours sebelumnya dengan hours yang baru akan ditambahkan
        $mergeHours = array_merge($hoursBefore, $request->hours);
        // jika ada jam duplikat, ambil salah satu
        $newHours = array_unique($mergeHours);

        // updateOrCreate([1], [2]) : mengecek berdasarkan array 1. jika ada maka update array 2, jika tidak ada tambahkan data dari array 1 dan 2

        $createData = Schedule::updateOrCreate([
            'cinema_id' => $request->cinema_id,
            'movie_id' => $request->movie_id,
        ], [
            'price' => $request->price,
            // jam penggabungan sebelum dan baru dari proses diatas
            'hours' => $newHours,
        ]);
        if ($createData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menambahkan data!');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data, coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule, $id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i',

        ], [
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi angka',
            'hours.*.required' => 'Jam tayang harus diisi',
            'hours.*.date_format' => 'Jam tayang diisi dengan jam:menit',
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours' => $request->hours,
        ]);

        if ($updateData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengubah data!');
        }
        return redirect()->back()->with('error', 'Gagal coba lagi!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, $id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menghapus data!');

    }

    public function export()
    {
        $fileName = 'data-jadwal.xlsx';
        return Excel::download(new ScheduleExport, $fileName);
    }

    public function trash()
    {
        $scheduleTrash = Schedule::with(['cinema', 'movie'])->onlyTrashed()->get();
        return view('staff.schedule.trash', compact('scheduleTrash'));
    }

    public function restore($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->restore();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }

    public function showSeats($scheduleId, $hourId)
    {
        $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
        $hour = $schedule['hours'][$hourId];

        // ambil data kursi dengan kriteria :
        // 1. sudah dibayar(ada paid_date di tickte payment)
        // 2. tiket dibeli di tgl dan jam sesuai dengan yang di klik
        $seats = Ticket::where('schedule_id', $scheduleId)->whereHas('ticketPayment',
        function($q) {
            // ambil tanggal sekarang
            $date = now()->format('Y-m-d');
            //whereDate : mencari berdasarkan tanggal
            $q->whereDate('paid_date', $date);
        })->whereTime('hour_id', $hour)->pluck('rows_of_seats');
        // pluck : mengambil data hanya satu kolom
        $seatsFormat = array_merge(...$seats);
        // ... : sread operator, mengekuarkan isi array, array_merge() menggabungkan isi array, jadi mengeluarkan dari dimensi kedua, digabungkan ke dimensi pertama
        // dd($seatsFormat);
        return view('schedule.show-seats', compact('schedule', 'hour', 'seatsFormat'));
    }
}
