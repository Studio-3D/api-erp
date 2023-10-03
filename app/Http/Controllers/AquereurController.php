<?php

namespace App\Http\Controllers;

use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreAquereurRequest;
use App\Http\Requests\UpdateAquereurRequest;
use App\Models\Aquereur;
use Illuminate\Support\Facades\Auth;

class AquereurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::guard('api')->check()){
            DatabaseHelper::Config();
            $aquereurs=Aquereur::on('temp')->get();
            return response()->json(['aquereurs',$aquereurs],200);
        }
        return response()->json(['error' => 'Unauthorized'],401);
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
    public function store(StoreAquereurRequest $request)
    {

        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $aquereur=new Aquereur();
            $aquereur->setConnection('temp');
            $aquereur->pourcentage=$request->pourcentage;
            $aquereur->client_id=$request->client_id;
            $aquereur->reservation_id=$request->reservation_id;
            if($aquereur->save()){
                return response()->json(['aquereur',$aquereur],200);
            }
        }
        return  response()->json(['error','Unauthorized'],401);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $aquereur=Aquereur::on('temp')->where('id',$id)->get();

            return response()->json(['aquereur'=>$aquereur],200);
        }
        return  response()->json(['error','Unauthorized'],401);
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
    public function update(UpdateAquereurRequest $request, $id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $aquereur=Aquereur::on('temp')->findOrFail($id);
            $update=$request->all();
            foreach ($update as $key => $value){
                $aquereur->$key = $value;
            }
            $aquereur->save();
            return response()->json(['aquereur'=>$aquereur],200);
        }
        return  response()->json(['error','Unauthorized'],401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $aquereur=Aquereur::on('temp')->findOrFail($id);

            if($aquereur->delete()){
                return response()->json(['message'=>'Aquereur deleted successfully'],200);
            }
            else{
                return response()->json(['message'=>'Aquereur non deleted '],400);
            }
        }
        return response()->json(['error'=>'Unauthorized'],401);
    }

    public function destoryAquereurUsingReservationId($reservation_id){
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $aquereurs=Aquereur::on('temp')->where('reservation_id',$reservation_id);
            if($aquereurs->delete()){
                return response()->json(['message'=>'Aquereurs deleted successfully'],200);
            }
            else{
                return response()->json(['message'=>'Aquereur non deleted '],400);
            }
        }
        return response()->json(['error'=>'Unauthorized'],401);
    }



    public function getAcquirerOfReservation($reservation_id){
        if(RoleHelper::ACSup()) {
            DatabaseHelper::Config();
            $aquereurs_reservation = Aquereur::on('temp')->where('reservation_id', $reservation_id)->get();
            if ($aquereurs_reservation->isEmpty()){
                return response()->json(['message'=>'Any Acquirer exists in this reservation'],400);
            }
            else return response()->json(['equereurs'=>$aquereurs_reservation],200);
        }
        return response()->json(['error','Unauthorized'],401);
    }

    public function  nbOfAcquirersInReservation($reservation_id)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $aquereurs_existe=Aquereur::on('temp')->where('reservation_id',$reservation_id)->get();
            if($aquereurs_existe->isEmpty()){
                return response()->json(['message'=>'Is there any acquirer existing in this reservation'],400);
            }
            else{
                $nb_of_aquereurs=$aquereurs_existe->count();
                return  response()->json(['nb_aquereur'=>$nb_of_aquereurs],200);
            }
        }
        return  response()->json(['error'=>'Unauthorized'],401);

    }
}
