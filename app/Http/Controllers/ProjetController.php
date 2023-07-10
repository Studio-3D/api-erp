<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjetRequest;
use App\Http\Requests\UpdateProjetRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use Illuminate\Http\Request;
use App\Http\Helpers\HistoriqueBienHelper;
use App\Models\Societe;


class ProjetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $projets = Projet::on('temp')->get();
            
            return response()->json(['message' => $projets]);
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
    public function store(StoreProjetRequest $request)
    {
        if (RoleHelper::Admin()) {
                       
            DatabaseHelper::Config();
            $projetData = [
                'nom' => $request->nom,
                'code' => $request->code,
                'adresse' => $request->adresse,
                'date_autorisation_construction' => $request->date_autorisation_construction,
                'date_permis_habiter' => $request->date_permis_habiter,
                'titre_foncier' => $request->titre_foncier,
                'surface_terrain' => $request->surface_terrain,
                'prix_acquisition' => $request->prix_acquisition,
                'limite_annulation_reservation' => $request->limite_annulation_reservation,
                'type_id' => $request->type_id,
                'nbre_tranches' => $request->nbre_tranches ?: 0,
                'nbre_blocs' => $request->nbre_blocs ?: 0,
                'nbre_immeubles' => $request->nbre_immeubles ?: 0,
                'nbre_biens' => $request->nbre_biens ?: 0,
            ];
            $projet = Projet::on('temp')->create($projetData);

            return response()->json(['message' => $projet], 200);
           
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $projet = Projet::on('temp')->findOrfail($id);
            return response()->json(['message' => $projet], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (RoleHelper::Admin()) {
            DatabaseHelper::Config();
            $projet = Projet::on('temp')->findOrfail($id);
            return response()->json(['message' => $projet], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjetRequest $request, $id)
    {
        if (RoleHelper::Admin()) {
            DatabaseHelper::Config();
            $projet = Projet::on('temp')->findOrfail($id);
            $projet->update($request->all());
            
            return response()->json(['message' => $projet], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Projet $projet)
    {
        if (RoleHelper::Admin()) {
            DatabaseHelper::Config();
            $projet = Projet::on('temp')->findOrfail($id);
            if ($projet->delete()) {
                return response()->json(['message' => 'Projet deleted succesfully'], 200);
            } else {
                return response()->json(['message' => 'Projet not deleted'], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }     
    }

    public function restoreProjet($projet_id)
    {
        if (RoleHelper::Admin()) {
            DatabaseHelper::Config();
            $projet = Projet::on('temp')->where('id', $projet_id)->withTrashed()->restore();
            return response()->json(['message' => 'Projet restored succesfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getTrashedProjets()
    {
        if (RoleHelper::Admin()) {
            DatabaseHelper::Config();
            $projet = Projet::on('temp')->onlyTrashed()->get();

            return response()->json(['message' => $projets], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
