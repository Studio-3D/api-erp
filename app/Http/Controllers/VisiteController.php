<?php

namespace App\Http\Controllers;

use App\Enum\TypeNotificationEnum;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreVisiteRequest;
use App\Models\User;
use App\Models\Visite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $visites = Visite::on('temp')->get();
            return response()->json(['visite' => $visites]);
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
    public function store(StoreVisiteRequest $request)
    {
        $user=Auth::user();
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $userAuth = User::on('temp')->where("user_id_origin",$user->id)->first();
            $visite = new Visite();
            $visite->setConnection('temp');
            $visite->user_id=$userAuth->id;
            $visite->prospect_id=$request->prospect_id;
            $visite->commentaire=$request->commentaire;
            $visite->source_id= $request->source_id;
            if($request->notifie==true)
            {
                $visite->notifie=$request->notifie;
                $visite->type_notification=$request->type_notification; // 1=sms,2=whatsapp,3=appel,4 = email
                if($visite->type_notification==TypeNotificationEnum::EMAIL){
                    $visite->email=$request->email;
                }
            }
            $visite->interet=$request->interet;
            $visite->bien_id = $request->bien_id;
            $visite->rdv = $request->rdv;
            $visite->status = $request->status;
            $visite->mode_relance=$request->mode_relance;
            $visite->date_relance=$request->date_relance;
            if($request->interet==1 && $visite->bien_id!=null || $request->interet==2)
            {
                $visite->save();
                return  response()->json(['message' => $visite], 200);
            }
            elseif ($request->interet==3)
            {
                $visite->save();
                return  response()->json(['message' => $visite], 200);
            }
            else return  response()->json(['erreur' => "Un champs obligatoire manque"], 520);

        }
        else  return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $visite = Visite::on('temp')->findOrfail($id);
            return response()->json(['message' => $visite], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visite $visite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visite $visite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visite $visite)
    {
        //
    }
}
