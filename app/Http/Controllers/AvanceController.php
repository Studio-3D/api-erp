<?php

namespace App\Http\Controllers;

use App\Enum\Statut;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreAvanceRequest;
use App\Http\Requests\UpdateAvanceRequest;
use App\Models\Avance;
use Illuminate\Support\Facades\Auth;

class AvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::guard('api')->check()){
            DatabaseHelper::Config();
            $avances=Avance::on('temp')->get();
            return response()->json(['avances'=>$avances],200);
        }
        return response()->json(['error'=>'Unauthorized'],401);
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
    public function store(StoreAvanceRequest $request)
    {

        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $avance= new Avance();
            $avance->setConnection('temp');
            $avance->sr= (bool)$request->sr;
            $avance->montant=$request->montant;
            $avance->mode_paiement=$request->mode_paiement;
            $avance->date_de_reglement=$request->date_de_reglement;
            $avance->echance=$request->echance;
            $avance->banque_id=$request->banque_id;
            $avance->reservation_id=$request->reservation_id;
            if(RoleHelper::Com()){
                $avance->statut=Statut::EN_ATTEND->value;
            }
            elseif(RoleHelper::AdminSup()){
                $avance->statut=Statut::VALIDER->value;
            }
            if($avance->save()){
                return $avance;
            }

        }
        return  response()->json(['error'=>'Unauthorized'],201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $avance=Avance::on('temp')->findOrFail($id);
            return response()->json(['avance'=>$avance],200);
        }
        return response()->json(['error','Unauthorized'],401);
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
    public function update(UpdateAvanceRequest $request,$id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $avance=Avance::on('temp')->findOrFail($id);
            $update=$request->all();
            foreach ($update as $key => $value){
                $avance->$key=$value;
            }
            $avance->save();
            return response()->json(['avance'=>$avance],200);
        }
        return  response()->json(['error','Unauthorized'],401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::config();
            $avance=Avance::on('temp')->findOrFail($id);
            if($avance->delete()){
                return response()->json(['message'=>'Avance deleted successfully']);
            }
            else{
                return response()->json(['message'=>'avance non deleted']);
            }
        }
        return response()->json(['error'=>'Unauthorized'],401);
    }

    public function destoryUsingReservationId($reservation_id){
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $avances=Avance::on('temp')->where('reservation_id',$reservation_id);
            if($avances->delete()){
                return response()->json(['message'=>'Avance deleted successfully'],200);
            }
            else{
                return response()->json(['message'=>'Avance non deleted '],400);
            }
        }
        return response()->json(['error'=>'Unauthorized'],401);
    }

    public function valideAvance($id){
        if(RoleHelper::AdminComptableSup()){
            DatabaseHelper::Config();
            $avance=Avance::on('temp')->findOrFail($id);
            if($avance->exists()){
                $avance->statut=Statut::VALIDER->value;
                if($avance->save())
                {
                    return response()->json(['message'=>'Advance has been validated'],200);
                }
                else{
                    return response()->json(['message'=>"Advance hasn't been validated."],400);
                }
            }
        }
        return  response()->json(['error'=>'Unauthorized'],401);
    }

    public function refuseAvance($id){

        if(RoleHelper::AdminComptableSup()) {
            DatabaseHelper::Config();
            $avance = Avance::on('temp')->findOrFail($id);
            if ($avance->exists) {
                $avance->statut = Statut::REFUSER->value;
                if ($avance->save()) {
                    return response()->json(['message' => 'The advance has been refused'], 200);
                } else {
                    return response()->json(['message' => "The advance hasn't been refused"], 400);
                }
            }
        }
        return response()->json(['error'=>'Unauthorized'],401);

    }
}
