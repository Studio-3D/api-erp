<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\NotifMenuEvent;
use App\Http\Controllers\Controller;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\FreinBienHelper;
use App\Http\Helpers\FreinEtageHelper;
use App\Http\Helpers\FreinOrientationHelper;
use App\Http\Helpers\FreinTrancheHelper;
use App\Http\Helpers\FreinTypologieHelper;
use App\Http\Helpers\FreinVueHelper;
use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\PaginationHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreFreinRequest;
use App\Http\Requests\Traite_Bien_freinRequest;
use App\Http\Requests\UpdateFreinRequest;
use App\Models\Frein;
use App\Models\FreinEtage;
use App\Models\FreinOrientation;
use App\Models\FreinTranche;
use App\Models\FreinTypologie;
use App\Models\FreinVue;
use App\Models\Frein_Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\User;

class FreinController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $freins = Frein::on('temp')->get();
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
        if (RoleHelper::ACSup()) {
            DatabaseHelper::Config();
            $frein = new Frein();
            $frein->setConnection('temp');
            $frein->prix_min = $request->prix_min;
            $frein->prix_max = $request->prix_max;
            $frein->superficie_min = $request->sup_min;
            $frein->superficie_max = $request->sup_max;
            $frein->etat = $request->etat;
            $frein->avance = $request->avance;
            $frein->visite_id = $request->visite_id;
            $frein->traite_appel_id = $request->traite_appel_id;
            $frein->tranche = empty($request->selectedTranches) ? false : true;
            $frein->etage = empty($request->selectedEtages) ? false : true;
            $frein->orientation = empty($request->selectedOrientations) ? false : true;
            $frein->vue = empty($request->selectedVues) ? false : true;
            $frein->typologie = empty($request->selectedTypologies) ? false : true;
            if ($frein->save()) {

                if (!empty($request->selectedTranches)) {
                    $tranches_array = explode(',', $request->selectedTranches); // $tranches_array sera ['5', '2']
                    foreach ($tranches_array as $valeur) {
                        FreinTrancheHelper::createFreinTranche($valeur, $frein->id);
                    }
                }
                if (!empty($request->selectedEtages)) {
                    $array_etage = explode(',', $request->selectedEtages); // $tranches_array sera ['5', '2']
                    foreach ($array_etage as $valeur) {
                        FreinEtageHelper::createFreinEtage($valeur, $frein->id);
                    }
                }
                if (!empty($request->selectedOrientations)) {
                    $array_orientation = explode(',', $request->selectedOrientations); // $tranches_array sera ['5', '2']
                    foreach ($array_orientation as $valeur) {
                        FreinOrientationHelper::createFreinOrientation($valeur, $frein->id);
                    }
                }
                if (!empty($request->selectedTypologies)) {
                    $array_typologie = explode(',', $request->selectedTypologies); // $tranches_array sera ['5', '2']
                    foreach ($array_typologie as $valeur) {
                        FreinTypologieHelper::createFreinTypologie($valeur, $frein->id);
                    }
                }
                if (!empty($request->selectedVues)) {
                    $array_vue = explode(',', $request->selectedVues); // $tranches_array sera ['5', '2']
                    foreach ($array_vue as $valeur) {
                        FreinVueHelper::createFreinVue($valeur, $frein->id);
                    }
                }
                return response()->json(['frein' => $frein], 200);
            }
            return response()->json(['error' => "Cette visite n'est pas du type perdu."], 520);
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
            $frein = Frein::on('temp')->findOrfail($id);
            if ($frein->exists()) {
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
            return response()->json(['frein' => $frein], 200);
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
            if (str_contains($request->freins, 'SUPERFICIE')==true) {
                $frein->superficie_min=$request->sup_min;
                $frein->superficie_max=$request->sup_max;
            }
            else{
                $frein->superficie_min=null;
                $frein->superficie_max=null;
            }

            if (str_contains($request->freins, 'PRIX')==true) {
                $frein->prix_min=$request->prix_min;
                $frein->prix_max=$request->prix_max;
            }
            else{
                $frein->prix_min=null;
                $frein->prix_max=null;
            }
            if (str_contains($request->freins, 'AVANCE')==true) {
                $frein->avance=$request->avance;
            }else{
                $frein->avance=null;
            }
            $frein->etat=$request->etat;
            $frein->tranche=str_contains($request->freins, 'TRANCHE')?false:true;
            $frein->etage=str_contains($request->freins, 'ETAGE')?false:true ;
            $frein->orientation= str_contains($request->freins, 'ORIENTATION')?false:true;
            $frein->vue=str_contains($request->freins, 'VUE')?false:true;
            $frein->typologie= str_contains($request->freins, 'TYPOLOGIE') ?false:true;
            $frein->save();
            FreinTrancheHelper::destroyFreinTranche($frein->id);
            if(!empty($request->selectedTranches) && str_contains($request->freins, 'TRANCHE') ){
                $tranches_array = explode(',', $request->selectedTranches); // $tranches_array sera ['5', '2']
                foreach($tranches_array as $valeur){
                        FreinTrancheHelper::createFreinTranche($valeur,$frein->id);
                }
            }
            FreinEtageHelper::destroyFreinEtage($frein->id);
            if (!empty($request->selectedEtages)&& str_contains($request->freins, 'ETAGE')) {
                $array_etage = explode(',', $request->selectedEtages); // $tranches_array sera ['5', '2']
                foreach ($array_etage as $valeur) {
                    FreinEtageHelper::createFreinEtage($valeur, $frein->id);
                }
            }
            FreinOrientationHelper::destroyFreinOrientation($frein->id);
            if (!empty($request->selectedOrientations)&& str_contains($request->freins, 'ORIENTATION')) {
                $array_orientation = explode(',', $request->selectedOrientations); // $tranches_array sera ['5', '2']
                foreach ($array_orientation as $valeur) {
                    FreinOrientationHelper::createFreinOrientation($valeur, $frein->id);
                }
            }
            FreinTypologieHelper::destroyFreinTypologie($frein->id);
            if (!empty($request->selectedTypologies)&& str_contains($request->freins, 'TYPOLOGIE')) {
                $array_typologie = explode(',', $request->selectedTypologies); // $tranches_array sera ['5', '2']
                foreach ($array_typologie as $valeur) {
                    FreinTypologieHelper::createFreinTypologie($valeur, $frein->id);
                }
            }
            FreinVueHelper::destroyFreinVue($frein->id);
            if (!empty($request->selectedVues)&& str_contains($request->freins, 'VUE')) {
                $array_vue = explode(',', $request->selectedVues); // $tranches_array sera ['5', '2']
                foreach ($array_vue as $valeur) {
                    FreinVueHelper::createFreinVue($valeur, $frein->id);
                }
            }
             //destroy frein bien dispo
             FreinBienHelper::destroyFreinBien($frein->id);
             //notification des biens disponible pour ce frein
             if($frein->visite_id!=null){
                NotificationHelper::destroy_notif_bien_dispo_frein($frein->visite_id);
             }
            return response()->json(['frein'=>$id]);
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
        if (RoleHelper::AdminSup()) {
            DatabaseHelper::Config();
            $frein = Frein::on('temp')->findOrFail($id);
            if ($frein->tranche) {
                $freinTranches = FreinTranche::on('temp')->where('frein_id', $id)->get();
                foreach ($freinTranches as $freinTranche) {
                    $freinTranche->delete();
                }
            }
            if ($frein->etage) {
                $freinEtages = FreinEtage::on('temp')->where('frein_id', $id)->get();
                foreach ($freinEtages as $freinEtage) {
                    $freinEtage->delete();
                }
            }
            if ($frein->orientation) {
                $freinOrientations = FreinOrientation::on('temp')->where('frein_id', $id)->get();
                foreach ($freinOrientations as $freinOrientation) {
                    $freinOrientation->delete();
                }
            }
            if ($frein->typologie) {
                $freinTypologies = FreinTypologie::on('temp')->where('frein_id', $id)->get();
                foreach ($freinTypologies as $freinTypologie) {
                    $freinTypologie->delete();
                }
            }
            if ($frein->vue) {
                $freinVues = FreinVue::on('temp')->where('frein_id', $id)->get();
                foreach ($freinVues as $freinVue) {
                    $freinVue->delete();
                }

            }
            //destroy frein bien dispo
            FreinBienHelper::destroyFreinBien($id);
            //notification des biens disponible pour ce frein
            NotificationHelper::destroy_notif_bien_dispo_frein($frein->visite_id);

            if ($frein->delete()) {
                return response()->json(['message' => 'Frein supprimé avec succès.'], 200);
            } else {
                return response()->json(['error' => "Le frein n'a pas été supprimé."], 404);
            }

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }

    private function syncRelationship($frein, $request, $relation, $modelClass, $pluckAtt)
    {
        if (!empty($frein->$relation)) {
            $frein->$relation()->sync($request);
        } else {
            $existingItems = $modelClass::on('temp')->where('frein_id', $frein->id)->pluck($pluckAtt)->toArray();
            if (!empty($existingItems)) {
                $frein->$relation()->detach($existingItems);
            }
        }
    }

    public function searchFreinByVisiteId($id, $text)
    {
        if ($text == 'without_row_deleted') {
            $frein = Frein::on('temp')->where('visite_id', $id)->first();
            if ($frein) {

                $frein_tranches = FreinTranche::on('temp')->where('frein_id', $frein->id)->get();
                if (count($frein_tranches) > 0) {
                    $frein['frein_tranches'] = $frein_tranches;
                }

                $frein_etages = FreinEtage::on('temp')->where('frein_id', $frein->id)->get();
                if (count($frein_etages)) {
                    $frein['frein_etages'] = $frein_etages;
                }

                $frein_vues = FreinVue::on('temp')->where('frein_id', $frein->id)->get();
                if (count($frein_vues)) {
                    $frein['frein_vues'] = $frein_vues;
                }

                $frein_typologies = FreinTypologie::on('temp')->where('frein_id', $frein->id)->get();
                if (count($frein_typologies)) {
                    $frein['frein_typologies'] = $frein_typologies;
                }

                $frein_orientations = FreinOrientation::on('temp')->where('frein_id', $frein->id)->get();
                if (count($frein_orientations)) {
                    $frein['frein_orientations'] = $frein_orientations;
                }

                return $frein;
            } else {
                return null;
            }

        } else {

            $frein = Frein::on('temp')->withTrashed()->where('visite_id', $id)->first();
            if ($frein) {

                $frein_tranches = FreinTranche::on('temp')->withTrashed()->where('frein_id', $frein->id)->get();
                if (count($frein_tranches) > 0) {
                    $frein['frein_tranches'] = $frein_tranches;
                }

                $frein_etages = FreinEtage::on('temp')->withTrashed()->where('frein_id', $frein->id)->get();
                if (count($frein_etages)) {
                    $frein['frein_etages'] = $frein_etages;
                }

                $frein_vues = FreinVue::on('temp')->withTrashed()->where('frein_id', $frein->id)->get();
                if (count($frein_vues)) {
                    $frein['frein_vues'] = $frein_vues;
                }

                $frein_typologies = FreinTypologie::on('temp')->withTrashed()->where('frein_id', $frein->id)->get();
                if (count($frein_typologies)) {
                    $frein['frein_typologies'] = $frein_typologies;
                }

                $frein_orientations = FreinOrientation::on('temp')->withTrashed()->where('frein_id', $frein->id)->get();
                if (count($frein_orientations)) {
                    $frein['frein_orientations'] = $frein_orientations;
                }

                return $frein;
            } else {
                return null;
            }

        }
    }



    public function get_clients_freins(Request $request, $projet_id)
    {

        if (Auth::guard('api')->check()) {
            // Default values for pagination null si non pas envoyer avec la raquete
            $size = $request->input('size', null);
            $page = $request->input('page', null);

            DatabaseHelper::Config();
            $user = Auth::user();
            $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
            $query = Frein::on('temp')->with('visite','visite.prospect')
                ->where('etat', 2)
                ->whereHas('visite', function ($q) use ($projet_id) {
                    $q->where('projet_id', $projet_id)->where('etat', 1);
                });
            if(!RoleHelper::AdminSup()){
                    $query->whereHas('visite', function ($q) use ($userAuth) {
                        $q->where('user_id', $userAuth->value('id'));
                    });
            }

            if ($request->filled('nom_prenom')){
                    $query->whereHas('visite.prospect', function ($q) use ($request) {
                    $q->where('nom', 'like', '%' . $request->input('nom_prenom') . '%')
                    ->orWhere('prenom', 'like', '%' . $request->input('nom_prenom') . '%');});
            }

            if ($request->filled('telephone')){
                $query->whereHas('visite.prospect', function ($q) use ($request) {
                $q->where('telephone', 'like', '%' . $request->input('telephone') . '%')
                ->orWhere('telephone_num2', 'like', '%' . $request->input('telephone') . '%');});
            }
            if ($request->filled('frein')) {
                $frein=strtolower($request->input('frein'));
                if(str_contains($frein, 'etage')){
                    $query->where('etage',1);
                } if(str_contains($frein, 'tranche')){
                    $query->where('tranche',1);
                }
                if(str_contains($frein, 'prix')){
                    $query->where(function ($q) {
                        $q->where('prix_min', '!=',null)->orwhere('prix_max', '!=',null);
                    });
                }
                if(str_contains($frein, 'superficie')){
                    $query->where(function ($q) {
                        $q->where('prix_min', '!=',null)->orwhere('prix_max', '!=',null);
                    });
                }
                if(str_contains($frein, 'avance')){
                    $query->where('avance',1);
                }
                if(str_contains($frein, 'orientation')){
                    $query->where('orientation',1);
                }
                if(str_contains($frein, 'vue')){
                    $query->where('vue',1);
                }
                if(str_contains($frein, 'typologie')){
                    $query->where('typologie',1);
                }
            }



            $clients=array();

            if(($query->count())>0) {
                $freins=$query->get();
                foreach ($freins as $fr) {
                    $fr_type=null;

                    //TRANCHE
                    if ($fr->tranche==1) {

                        if($fr_type==null){
                            $fr_type.='TRANCHE';
                           }else{
                            $fr_type.=',TRANCHE';
                        }
                    }

                    //ETAGES
                    if ($fr->etage==1) {
                        if($fr_type==null){
                            $fr_type.='ETAGE';
                           }else{
                            $fr_type.=',ETAGE';
                           }
                    }
                    //orientation
                    if ($fr->orientation==1) {
                        if($fr_type==null){
                            $fr_type.='ORIENTATION';
                           }else{
                            $fr_type.=',ORIENTATION';
                           }
                    }
                    //TYPOLOGIE
                    if ($fr->typologie==1) {
                        if($fr_type==null){
                            $fr_type.='TYPOLOGIE';
                           }else{
                            $fr_type.=',TYPOLOGIE';
                           }
                    }
                    //VUE
                    if ($fr->vue==1) {
                        if($fr_type==null){
                            $fr_type.='VUE';
                           }else{
                            $fr_type.=',VUE';
                           }
                    }
                    //avance
                    if ($fr->avance!=null) {
                        if($fr_type==null){
                            $fr_type.='AVANCE';
                           }else{
                            $fr_type.=',AVANCE';
                        }
                    }
                    //PRIX
                    if ($fr->prix_min!=null ||  $fr->prix_max!=null) {
                        if($fr_type==null){
                            $fr_type.='PRIX';
                           }else{
                            $fr_type.=',PRIX';
                           }
                    }

                    //SUPERFICIE
                    if ($fr->superficie_min!=null && $fr->superficie_max!=null) {
                        if($fr_type==null){
                            $fr_type.='SUPERFICIE';
                           }else{
                            $fr_type.=',SUPERFICIE';
                           }
                    }


                    array_push($clients,array('id' => $fr->id,'date' => $fr->created_at,'nom' => $fr->visite->prospect->nom,'prenom' => $fr->visite->prospect->prenom,'telephone' => $fr->visite->prospect->telephone,'telephone_2' => $fr->visite->prospect->telephone_num2,'id_origin' => $fr->visite->origin_id,'frein'=>$fr_type));
                 }
            }


              // Paginate the array of visites
              $data = PaginationHelper::paginate_array($clients, $size, $page, $request->url());

              $items = $data->items();

              $pagination = [
                  'currentPage' => $data->currentPage(),
                  'totalItems' => $data->total(),
                  'totalPages' => $data->lastPage(),
              ];

              return response()->json([
                  'data' => $items,
                  'pagination' => $pagination,
              ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function biens_by_frein(Request $request, $frein_id)
    {
        if (Auth::guard('api')->check()) {
            $size = $request->input('size', null);
            $page = $request->input('page', null);
            DatabaseHelper::Config();

            // Démarrer la requête directement sur le modèle
            $query = Frein_Bien::on('temp')->where('frein_id', $frein_id)->with('is_proposed','bien');

            if ($request->filled('bien_filtre')) {
                $query->whereHas('bien', function ($q) use ($request) {
                    $q->where('propriete_dite_bien', $request->bien_filtre);
                });
            }
            if ($request->filled('numero_filtre')) {
                $query->whereHas('bien', function ($q) use ($request) {
                    $q->where('numero', $request->numero_filtre);
                });
            }
            if ($request->filled('orientation_filtre')) {
                $query->whereHas('bien', function ($q) use ($request) {
                    $q->where('orientation', $request->orientation_filtre);
                });
            }

            if ($request->filled('type_filtre')) {
                $query->whereHas('bien.typeBien', function ($q) use ($request) {
                    $q->where('type', $request->type_filtre);
                });
            }
            if (is_numeric($size) && is_numeric($page) && $size > 0 && $page > 0) {
                $biens = $query->orderBy('created_at', 'desc')
                    ->paginate($size, ['*'], 'page', $page);

                // Extraire les propriétés du paginateur
                $pagination = [
                    'currentPage' => $biens->currentPage(),
                    'totalItems' => $biens->total(),
                    'totalPages' => $biens->lastPage(),
                ];

                // Extraire les éléments d'utilisateur du paginateur
                $biens = $biens->items();

                // Retourner la réponse simplifiée
                return response()->json([
                    'data' => $biens,
                    'pagination' => $pagination,
                ], 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function traiter_bien_frein(Traite_Bien_freinRequest $request, $bien_id, $frein_id)
    {

        if (RoleHelper::ACSup()) {
            DatabaseHelper::Config();
            $frein = Frein::on('temp')->findOrFail($frein_id);
            if ($request->pre_reserve == 1) {
                $bien = new BienController();
                $bien->prereserverBien($bien_id, null, null);
            }
            $frein->etat = 3;
            $frein->commentaire = $request->commentaire;
            if ($frein->save()) {
                //destroy frein bien
                FreinBienHelper::destroyFreinBien($frein_id);
                //notification des biens disponible pour ce frein
                NotificationHelper::destroy_notif_bien_dispo_frein($frein->visite_id);
            }
            Config::set('broadcasting.default', 'pusher_5');
            broadcast(new NotifMenuEvent('C'));

            return response()->json(['message' => $frein], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }


    /**************************************Apppellls********************** */
    public function searchFreinByAppelId($id,$text){
        if($text=='without_row_deleted'){
            $frein=Frein::on('temp')->where('traite_appel_id',$id)->first();
            if($frein){

                    $frein_tranches=FreinTranche::on('temp')->where('frein_id',$frein->id)->get();
                    if(count($frein_tranches)>0){
                        $frein['frein_tranches']=$frein_tranches;
                    }

                    $frein_etages=FreinEtage::on('temp')->where('frein_id',$frein->id)->get();
                    if(count($frein_etages)){
                        $frein['frein_etages']=$frein_etages;
                    }


                    $frein_vues=FreinVue::on('temp')->where('frein_id',$frein->id)->get();
                    if(count($frein_vues)){
                        $frein['frein_vues']=$frein_vues;
                    }



                    $frein_typologies=FreinTypologie::on('temp')->where('frein_id',$frein->id)->get();
                    if(count($frein_typologies)){
                        $frein['frein_typologies']=$frein_typologies;
                    }

                    $frein_orientations=FreinOrientation::on('temp')->where('frein_id',$frein->id)->get();
                    if(count($frein_orientations)){
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


    public function desactiver_freins($param,Request $request)
    {
        if (RoleHelper::ACSup()) {
            DatabaseHelper::Config();
            $exit=0;
            foreach ($request->list_freins as $key => $list) {
                //Annuler perdu

                if ($list['action'] == 2) {
                    $exit=1;
                    $frein = Frein::on('temp')->findorfail($list['fr_id']);
                    $frein->etat = 4;
                    if ($frein->save()) {
                        //destroy frein bien
                        FreinBienHelper::destroyFreinBien($frein->id);
                        //notification des biens disponible pour ce frein
                        NotificationHelper::destroy_notif_bien_dispo_frein($frein->visite_id);
                    }

                }
            }
            if($exit==1){
                Config::set('broadcasting.default', 'pusher_5');
                broadcast(new NotifMenuEvent('C'));
            }

            return response()->json(['message' => 'suceees'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }
}
