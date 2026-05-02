<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UnitController extends Controller
{
    /**
     * Display a listing of all units.
     * GET /admin/units
     */
    public function index()
    {
        $units = Unit::all();
        return view('unitsmanagement', [
            'units' => $units,
            'activePage' => 'Units',
            'titlePage' => 'Units',
        ]);
    }

    /**
     * Show the form for creating or editing a unit.
     * GET /admin/unit-form/{unit_id}
     */
    public function addEditUnit($unit_id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $unit = $unit_id == 'new' ? new Unit() : Unit::find($unit_id);
        $units = Unit::where('id', '!=', optional($unit)->id)->get();

        return view('unit-form', [
            'unit' => $unit,
            'units' => $units,
            'activePage' => 'Unit',
            'titlePage' => 'Unit',
        ]);
    }

    /**
     * Store a newly created unit or update an existing one.
     * POST /admin/saveunit
     */
    public function save(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        try {
            $unitId = $request->input('unit_id');

            if ($unitId) {
                $unit = Unit::find($unitId);
            } else {
                $unit = new Unit();
            }

            $unit->name = $request->input('name');
            $unit->description = $request->input('description');
            $unit->related_unit_id = $request->input('related_unit_id');
            $unit->related_unit_quantity = $request->input('related_unit_quantity');

            $unit->save();

            Session::flash('alert-success', 'Unit saved successfully!');
        } catch (\Exception $e) {
            Session::flash('alert-danger', 'Error has occurred: ' . $e->getMessage());
        }

        return redirect('/admin/units');
    }

    /**
     * Delete a unit.
     * POST /admin/unit/delete
     */
    public function deleteUnit(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        try {
            $unit = Unit::find($request->input('unit_id'));

            if ($unit) {
                $unit->delete();
                Session::flash('alert-success', 'Unit deleted successfully!');
            } else {
                Session::flash('alert-danger', 'Unit not found...');
            }
        } catch (\Exception $e) {
            Session::flash('alert-danger', 'Error has occurred: ' . $e->getMessage());
        }

        return redirect('/admin/units');
    }

    /**
     * Display the specified unit details.
     * GET /admin/unit/{unit_id}
     */
    public function viewUnit($unit_id)
    {
        $unit = Unit::find($unit_id);
        return view('unitdetails', ['unit' => $unit]);
    }
}
