<?php

namespace App\Http\Controllers;

use App\Http\Helpers\DatabaseHelper;
use App\Http\Requests\StoreSocieteRequest;
use App\Http\Requests\UpdateSocieteRequest;
use App\Models\Societe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocieteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('api')->check() && Auth::guard('api')->user()->type == 1) {
            $societes = Societe::all();
            return response()->json(['societe' => $societes]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('societe');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSocieteRequest $request)
    {
        if (Auth::guard('api')->check() && Auth::guard('api')->user()->type == 1) {
           

            //  if we have  fillable  in  model  must  be  use  that method is fast  and  easy sometimes
            //$sociate=Societe::create($validateData);

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoPath = $logo->store('logos', 'public');
                $request['logo'] = $logoPath;
            }
            $societe = new Societe();
            if (Societe::where('raison_sociale', $request['raison_sociale'])->exists()) {
                return response()->json(['message' => 'Raison sociale already exists'], 400);
            } else {
                $societe->raison_sociale = $request['raison_sociale'];
                $societe->adresse = $request['adresse'];
                $societe->nom_contact = $request['nom_contact'];
                $societe->prenom_contact = $request['prenom_contact'];
                $societe->tel = $request['tel'];
                $societe->email = $request['email'];
                $societe->logo = $request['logo'];
                $societe->save();
            }
            $societe->raison_sociale = $request['raison_sociale'];
            $societe->adresse = $request['adresse'];
            $societe->nom_contact = $request['nom_contact'];
            $societe->prenom_contact = $request['prenom_contact'];
            $societe->tel = $request['tel'];
            $societe->email = $request['email'];
            $societe->logo = $request['logo'];
            $societe->save();

            $projectdata = new DatabaseHelper();
            $response = $projectdata->createNewClientDatabase($societe->raison_sociale);
            if ($response->getStatusCode() == 200) {
                return response()->json(['message' => $response->getOriginalContent()['message']]);
            } else {
                return response()->json(['message' => $response->getOriginalContent()['message']]);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(Societe $societe)
    {
        return $societe;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Societe $societe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSocieteRequest $request, Societe $societe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Societe $societe)
    {
        //
    }
}
