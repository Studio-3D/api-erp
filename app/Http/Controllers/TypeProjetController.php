<?php

namespace App\Http\Controllers;

use App\Models\TypeProjet;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeProjetRequest;
use App\Http\Requests\UpdateTypeProjetRequest;
use Illuminate\Support\Facades\Auth;



class TypeProjetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            $typeprojets = Typeprojet::all();
            return response()->json(['message' => $typeprojets]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    
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
    public function store(StoreTypeProjetRequest $request)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            
            $typeprojet = new typeprojet();
            $typeprojet->type = $request->type;
            $typeprojet->save();

            return response()->json(['message' => 'ce type de projet creer avec succes'], 200);
           
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeProjet $typeProjet)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            return response()->json(['message' => $typeProjet], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeProjet $typeProjet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTypeProjetRequest $request, TypeProjet $typeProjet)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
      
            $typeProjet->update($request->all());
            
            return response()->json(['message' => 'type projet updated succesfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeProjet $typeProjet)
    {
        if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {
            
            if ($typeProjet->delete()) {
                return response()->json(['message' => 'ce type de projet deleted succesfully'], 200);
            } else {
                return response()->json(['message' => 'ce type de projet non deleted'], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);

        }
    }

    public function restoreTypeProjet($typeprojet_id)
    {
        if (Auth::guard('api')->check() && Auth::guard('api')->user()->type == 1) {

            TypeProjet::where('id', $typeprojet_id)->withTrashed()->restore();

            return response()->json(['message' => 'Type projet est projet restaurer'], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getTrashedTypesProjet()
    {

        if (Auth::guard('api')->check() && Auth::guard('api')->user()->type == 1) {
            $typeProjets = TypeProjet::onlyTrashed()->get();

            return response()->json(['message' => $typeProjets], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
