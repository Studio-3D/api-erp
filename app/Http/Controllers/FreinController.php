<?php

namespace App\Http\Controllers;

use App\Enum\InteretEnum;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\FreinEtageHelper;
use App\Http\Helpers\FreinOrientationHelper;
use App\Http\Helpers\FreinTrancheHelper;
use App\Http\Helpers\FreinTypologieHelper;
use App\Http\Helpers\FreinVueHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreFreinRequest;
use App\Models\Frein;
use App\Models\Visite;
use Illuminate\Http\Request;

class FreinController extends Controller
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
    public function store(StoreFreinRequest $request)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $frein=new Frein();
            $frein->setConnection('temp');
            $frein->prix_min=$request->prix_min;
            $frein->prix_max=$request->prix_max;
            $frein->superficie_min=$request->superficie_min;
            $frein->superficie_max=$request->superficie_max;
            $frein->liste_attente=$request->liste_attente;
            $frein->avance=$request->avance;
            $frein->visite_id=$request->visite_id;
            $frein->tranche=$request->selectedTranches?true:false;
            $frein->etage=$request->selectedEtages?true:false;
            $frein->orientation=$request->selectedOrientations?true:false;
            $frein->vue=$request->selectedVues?true:false;
            $frein->typologie=$request->selectedTypologies?true:false;
            $visite=Visite::on('temp')->where('id',$frein->visite_id)->get()->value('interet');
            if($visite== InteretEnum::PERDU->name){
                $frein->save();
                if($request->selectedTranches){
                    foreach($request->selectedTranches as $valeur){
                      FreinTrancheHelper::createFreinTranche($valeur,$frein->id);
                    }
                }
                if($request->selectedEtages){
                    foreach($request->selectedEtages as $valeur){
                        FreinEtageHelper::createFreinEtage($valeur,$frein->id);
                    }
                }
                if($request->selectedOrientations){
                    foreach($request->selectedOrientations as $valeur){
                        FreinOrientationHelper::createFreinOrientation($valeur,$frein->id);
                    }
                }
                if($request->selectedTypologies){
                    foreach($request->selectedTypologies as $valeur){
                        FreinTypologieHelper::createFreinTypologie($valeur,$frein->id);
                    }
                }
                if($request->selectedVues){
                    foreach($request->selectedVues as $valeur){
                        FreinVueHelper::createFreinVue($valeur,$frein->id);
                    }
                }
                return response()->json(['frein' => $frein], 200);
            }
            return response()->json(['error' => "cette visite n'est pas de type perdu"], 520);
        }
        else return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
