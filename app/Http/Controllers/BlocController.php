<?php

namespace App\Http\Controllers;

use App\Models\Bloc;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlocRequest;
use App\Http\Requests\UpdateBlocRequest;
use Illuminate\Support\Facades\Auth;



class BlocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreBlocRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Bloc $bloc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bloc $bloc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlocRequest $request, Bloc $bloc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bloc $bloc)
    {
        //
    }
    public function restoreBloc($bloc_id)
    {
        if (Auth::guard('api')->check() && Auth::guard('api')->user()->type == 1) {

            Bloc::where('id', $bloc_id)->withTrashed()->restore();

            return response()->json(['message' => 'Bloc est bien restaurer'], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getTrashedBlocs()
    {

        if (Auth::guard('api')->check() && Auth::guard('api')->user()->type == 1) {
            $blocs = Bloc::onlyTrashed()->get();

            return response()->json(['message' => $blocs], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
