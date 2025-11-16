<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PromoExport;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index', compact('promos'));
    }

    public function datatables()
    {
        $promos = Promo::query();
        return DataTables::of($promos)->addIndexColumn()->addColumn('discount', function($promo) {
            if ($promo['type'] == 'percent') {
                return $promo['discount'].'%';
            } return 'Rp' . ' ' . number_format($promo['discount'], 0, ',', '.');
        })->addColumn('action', function($promo) {
            $btnEdit = '<a href="' . route('staff.promos.edit', $promo->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('staff.promos.delete', $promo->id) . '" method="POST">
                            ' . @csrf_field() . @method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger m-2">Hapus</button>
                        </form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2"> ' . $btnEdit . $btnDelete . '</div>';
        })->rawColumns(['discount', 'action'])->make(true);

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.promo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|unique:promos,promo_code',
            'type'       => 'required|in:percent,rupiah',
            'discount'   => 'required|numeric|min:1',
        ]);

        if ($request->type === 'percent' && $request->discount > 100) {
            return back()->withErrors(['discount' => 'Diskon dalam persen tidak boleh lebih dari 100'])->withInput();
        }

        if ($request->type === 'rupiah' && $request->discount < 1000) {
            return back()->withErrors(['discount' => 'Diskon dalam rupiah minimal Rp 1.000'])->withInput();
        }

        Promo::create([
            'promo_code' => $request->promo_code,
            'type'       => $request->type,
            'discount'   => $request->discount,
            'actived'    => 1
        ]);

        return redirect()->route('staff.promos.index')->with('success', 'Promo berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $promo = Promo::find($id);
        return view('staff.promo.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'promo_code' => 'required|unique:promos,promo_code,' . $id,
            'discount'   => 'required|numeric|min:1',
            'type'       => 'required|in:percent,rupiah',
        ]);

        // Validasi tambahan sama kaya di store
        if ($request->type === 'percent' && $request->discount > 100) {
            return back()->withErrors(['discount' => 'Diskon dalam persen tidak boleh lebih dari 100'])->withInput();
        }

        if ($request->type === 'rupiah' && $request->discount < 1000) {
            return back()->withErrors(['discount' => 'Diskon dalam rupiah minimal Rp 1.000'])->withInput();
        }

        $promo = Promo::findOrFail($id);
        $promo->update([
            'promo_code' => $request->promo_code,
            'discount'   => $request->discount,
            'type'       => $request->type,
        ]);

        return redirect()->route('staff.promos.index')->with('success', 'Promo berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $promo = Promo::find($id);
        $promo->delete();
        return redirect()->route('staff.promos.index')->with('success', 'Promo berhasil dihapus');
    }

    public function export()
    {
        $fileName = 'data-promo.xlsx';
        return Excel::download(new PromoExport, $fileName);
    }

    public function trash()
    {
        $promoTrash = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promoTrash'));
    }

    public function restore($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->restore();
        return redirect()->route('staff.promos.index')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }

}
