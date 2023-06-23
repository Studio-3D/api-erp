<?php

namespace App\Http\Controllers;

use App\Models\CompositionBien;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompositionBienRequest;
use App\Http\Requests\UpdateCompositionBienRequest;
use Illuminate\Support\Facades\Auth;


class CompositionBienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('api')->check()) {
            $CompositionBiens = CompositionBien::all();
            return response()->json(['message' => $CompositionBiens]);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompositionBienRequest $request)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            
            $composition_bien = new CompositionBien();
            $composition_bien->bien_id = $request->bien_id;
            $composition_bien->nbre_chambres = $request->nbre_chambres;
            $composition_bien->nbre_salons = $request->nbre_salons;
            $composition_bien->nbre_sdb = $request->nbre_sdb;
            $composition_bien->nbre_cuisines = $request->nbre_cuisines;
            $composition_bien->nbre_halls = $request->nbre_halls;
            $composition_bien->nbre_terasses = $request->nbre_terasses;
            $composition_bien->nbre_balcons = $request->nbre_balcons;
            $composition_bien->nbre_buanderies = $request->nbre_buanderies;
            $composition_bien->nbre_placards = $request->nbre_placards;
            $composition_bien->nbre_receptions = $request->nbre_receptions;
            $composition_bien->save();

            return response()->json(['message' => $composition_bien], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CompositionBien $compositionBien)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            return response()->json(['message' => $compositionBien], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompositionBien $compositionBien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompositionBienRequest $request, CompositionBien $compositionBien)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            $compositionBien->update($request->all());
            return response()->json(['message' => $compositionBien], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompositionBien $compositionBien)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {

            if ($compositionBien->delete()) {
                return response()->json(['message' => 'composition Bien deleted succesfully'], 200);
            } else {
                return response()->json(['message' => 'composition Bien non deleted'], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);

        }
    }

    public function restoreCompositionBien($compositionBien_id)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {

            CompositionBien::where('id', $compositionBien_id)->withTrashed()->restore();

            return response()->json(['message' => 'composition Bien est bien restaurer'], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getTrashedCompositionBiens()
    {

        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            $compositionBiens = CompositionBien::onlyTrashed()->get();

            return response()->json(['message' => $compositionBiens], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getComposition($bien_id)
    {  
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2 || Auth::guard('api')->user()->type == 3)) {
            $CompositionBien = CompositionBien::where('bien_id', $bien_id)->get();
            return response()->json(['message' => $CompositionBien], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

}
