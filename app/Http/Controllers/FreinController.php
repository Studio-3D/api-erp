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
use App\Http\Requests\UpdateFreinRequest;
use App\Models\Frein;
use App\Models\FreinEtage;
use App\Models\FreinOrientation;
use App\Models\FreinTranche;
use App\Models\FreinTypologie;
use App\Models\FreinVue;
use App\Models\Visite;

use Illuminate\Support\Facades\Auth;

class FreinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $freins= Frein::on('temp')->get();
            return response()->json(['freins' => $freins]);
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
            $visite=Visite::on('temp')->where('id',$request->visite_id)->get()->value('interet');
            if($visite == InteretEnum::PERDU->name){
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
            return response()->json(['error' => "Cette visite n'est pas du type perdu."], 520);
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
            $frein= Frein::on('temp')->findOrfail($id);
            if($frein->exists()) {
                if ($frein->value('tranche') == true) {
                    $frein_tranches = FreinTranche::on('temp')->where('frein_id', $frein->id)->get();
                    $frein['frein_tranches'] = $frein_tranches;
                }
                if ($frein->value('etage') == true) {
                    $frein_etages = FreinEtage::on('temp')->where('frein_id', $frein->id)->get();
                    $frein['frein_etages'] = $frein_etages;
                }
                if ($frein->value('vue') == true) {
                    $frein_vues = FreinVue::on('temp')->where('frein_id', $frein->id)->get();
                    $frein['frein_vues'] = $frein_vues;
                }
                if ($frein->value('typologie') == true) {
                    $frein_typologies = FreinVue::on('temp')->where('frein_id', $frein->id)->get();
                    $frein['frein_typologies'] = $frein_typologies;
                }
                if ($frein->value('orientation') == true) {
                    $frein_orientations = FreinOrientation::on('temp')->where('frein_id', $frein->id)->get();
                    $frein['frein_orientations'] = $frein_orientations;
                }
            }
            return response()->json(['frein'=>$frein], 200);
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
    public function update(UpdateFreinRequest $request, $id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $frein=Frein::on('temp')->findOrFail($id);
            $frein->prix_min=$request->prix_min;
            $frein->prix_max=$request->prix_max;
            $frein->superficie_min=$request->superficie_min;
            $frein->superficie_max=$request->superficie_max;
            $frein->liste_attente=$request->liste_attente;
            $frein->avance=$request->avance;
            $frein->tranche=$request->selectedTranches?true:false;
            $frein->etage=$request->selectedEtages?true:false;
            $frein->orientation=$request->selectedOrientations?true:false;
            $frein->vue=$request->selectedVues?true:false;
            $frein->typologie=$request->selectedTypologies?true:false;
            $frein->save();
            $this->syncRelationship($frein, $request->selectedTranches, 'tranche', FreinTranche::class,'tranche_id');
            $this->syncRelationship($frein, $request->selectedEtages, 'etage', FreinEtage::class,'etage');
            $this->syncRelationship($frein, $request->selectedOrientations, 'orientation', FreinOrientation::class,'orientation');
            $this->syncRelationship($frein, $request->selectedTypologies, 'typologie', FreinTypologie::class,'typologie_id');
            $this->syncRelationship($frein, $request->selectedVues, 'vue', FreinVue::class,'vue_id');
            return response()->json(['frein'=>$frein],200);
        }
        else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(RoleHelper::AdminSup()){
            DatabaseHelper::Config();
            $frein=Frein::on('temp')->findOrFail($id);
            if($frein->delete()){
                return response()->json(['messqge'=>'Frein supprimé avec succès.'],200);
            }
            else return response()->json(['error'=>"Le frein n'a pas été supprimé."],404);
        }
        else return response()->json(['error' => 'Unauthorized'], 401);
    }

    private function syncRelationship($frein, $request, $relation, $modelClass,$pluckAtt)
    {
        if ($frein->$relation) {
              $frein->$relation()->sync($request);
        } else {
            $existingItems = $modelClass::on('temp')->where('frein_id', $frein->id)->pluck($pluckAtt)->toArray();
            if (!empty($existingItems)) {
                $frein->$relation()->detach($existingItems);
            }
        }
    }

    public function searchFreinByVisiteId($id){
        $frein=Frein::on('temp')->where('visite_id',$id)->first();
        if($frein){
            if($frein->value('tranche')==true)
            {
                $frein_tranches=FreinTranche::on('temp')->where('frein_id',$frein->id)->get();
                $frein['frein_tranches']=$frein_tranches;
            }
            if($frein->value('etage')==true)
            {
                $frein_etages=FreinEtage::on('temp')->where('frein_id',$frein->id)->get();
                $frein['frein_etages']=$frein_etages;
            }
            if($frein->value('vue')==true)
            {
                $frein_vues=FreinVue::on('temp')->where('frein_id',$frein->id)->get();
                $frein['frein_vues']=$frein_vues;
            }
            if($frein->value('typologie')==true)
            {
                $frein_typologies=FreinVue::on('temp')->where('frein_id',$frein->id)->get();
                $frein['frein_typologies']=$frein_typologies;
            }
            if($frein->value('orientation')==true)
            {
                $frein_orientations=FreinOrientation::on('temp')->where('frein_id',$frein->id)->get();
                $frein['frein_orientations']=$frein_orientations;
            }
            return $frein;
        }
        else
        {
            return null;
        }

    }
}
