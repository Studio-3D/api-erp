<?php

namespace App\Http\Controllers;

use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreProspectRequest;
use App\Models\Prospect;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;

class ProspectController extends Controller
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
    public function store(StoreProspectRequest $request)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $prospect= new Prospect();
            $prospect->setConnection("temp");
            $prospect->cin=$request->cin;
            $prospect->nom=$request->nom;
            $prospect->prenom=$request->prenom;
            $prospect->telephone=$request->telephone;
            $prospect->telephone_num2=$request->telephone_num2;
            $prospect->save();
            return $prospect->id;
        }
        else return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $prospect = Prospect::on('temp')->findOrfail($id);
            return response()->json(['message' => $prospect], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
