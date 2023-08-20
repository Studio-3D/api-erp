<?php

namespace App\Http\Controllers;

use App\Enum\InteretEnum;
use App\Enum\TypeNotificationEnum;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreFreinRequest;
use App\Http\Requests\StoreProspectRequest;
use App\Http\Requests\StoreVisiteRequest;
use App\Models\Prospect;
use App\Models\User;
use App\Models\Visite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;

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
        $user = Auth::user();
        if (RoleHelper::ACSup()) {
            DatabaseHelper::Config();
            $prospectExist = Prospect::on('temp')->where('cin', $request->cin)->get()->value('id');
            if ($prospectExist ) {
                $visiteExist = Visite::on('temp')->where('prospect_id', $prospectExist->value('id'))->get();
                if($visiteExist) {
                    return response()->json(['message' => 'Ce prospect existe déjà, il possède une visite'], 520);
                }
            } else {
                $validatedData = $request->validated();
                $prospectController = new ProspectController();
                $prospectExist = $prospectController->store(new StoreProspectRequest($validatedData));
            }
            $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
            $visite = new Visite();
            $visite->setConnection('temp');
            $visite->user_id = $userAuth->value('id');
            $visite->prospect_id = $prospectExist;
            $visite->commentaire = $request->commentaire;
            $visite->source_id = $request->source_id;
            $visite->notifie = $request->notifie;
            $visite->type_notification = $request->type_notification;
            $visite->interet = $request->interet;
            $visite->bien_id = $request->bien_id;
            $visite->rdv = $request->rdv;
            $visite->status = $request->status;
            $visite->mode_relance = $request->mode_relance;
            $visite->date_relance = $request->date_relance;
            $visite->save();
            if ($visite->interet == InteretEnum::PERDU->value) {
                $freinRequest=$request->validated();
                $freinRequest['visite_id']=$visite->getAttribute('id');
                $freinRequest['selectedTranches']=$request->selectedTranches;
                $freinRequest['selectedEtages']=$request->selectedEtages;
                $freinRequest['selectedOrientations']=$request->selectedOrientations;
                $freinRequest['selectedTypologies']=$request->selectedTypologies;
                $freinRequest['selectedVues']=$request->selectedVues;
                $freinController = new FreinController();
                $freinController->store(new StoreFreinRequest($freinRequest));
            }
            return response()->json(['message' => $visite], 200);
        }
        else
        {
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
