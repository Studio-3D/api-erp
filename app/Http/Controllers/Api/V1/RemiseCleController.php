<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use App\Models\RemiseCle;
use App\Models\Societe;
use App\Models\Bien;
use App\Models\StatutClient;
use App\Http\Helpers\FichierHelper;  // AJOUTER CETTE LIGNE

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use DB;

class RemiseCleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function indexByProjet(Request $request, $projet_id)
    {
         if (RoleHelper::ACSup()||RoleHelper::RespoLivraison()) {
            // Default values for pagination null si non pas envoyer avec la raquete
            $size = $request->input('size', null);
            $page = $request->input('page', null);

            DatabaseHelper::Config();

            $query = RemiseCle::on('temp')->with('bien','userRemis')->where('remise_cles.projet_id', $projet_id);
            if (RoleHelper::Com()) {
                $user     = Auth::user();
                $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
                $query->where('user_id_remis', $userAuth->value('id'));
            }

             if ($request->filled('bien')) {
            $query->whereHas('bien', function ($q) use ($request) {
                $q->where('propriete_dite_bien', 'like', '%' . $request->input('bien') . '%');
            });
            }

             if ($request->filled('cc')) {
                $query->whereHas('userRemis', function ($q) use ($request) {
                    $q->where(function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->input('cc') . '%')
                            ->orWhere('prenom', 'like', '%' . $request->input('cc') . '%');
                    });
                });
            }
            /*if ($request->filled('client')) {
                $query->whereHas('bien.reservation.aquereurs.client', function ($q) use ($request) {
                    $q->where(function ($q) use ($request) {
                        $q->where('nom', 'like', '%' . $request->input('client') . '%')
                            ->orWhere('prenom', 'like', '%' . $request->input('client') . '%');
                    });
                });

             }*/
            if ($request->filled('date_remise')) {
                $start = Carbon::parse($request->input('date_remise'));
                $query->whereDate('remise_cles.date_remise', $start);
            }

            // Check if pagination parameters are provided and valid
            if (is_numeric($size) && is_numeric($page) && $size > 0 && $page > 0) {
                // Paginate the query results
                $remis = $query->LeftJoin('reservations', 'reservations.bien_id', 'remise_cles.bien_id')
                    ->select('remise_cles.*', 'reservations.id as id_res','reservations.code_reservation as code_reservation')
                    ->where('reservations.etat', 1)
                    ->where('reservations.deleted_at', null)
                    ->orderBy('remise_cles.created_at', 'desc')
                    ->paginate($size, ['*'], 'page', $page);

                $pagination = [
                    'currentPage' => $remis->currentPage(),
                    'totalItems'  => $remis->total(),
                    'totalPages'  => $remis->lastPage(),
                ];

                $Items = $remis->items();

                // Return the response with pagination
                return response()->json([
                    'data'       => $Items,
                    'pagination' => $pagination,
                ], 200);
            } else {
                // Return all results if pagination parameters are not provided or invalid
                $remis = $query->orderBy('created_at', 'desc')
                    ->get();

                return response()->json(['remis' => $remis], 200);
            }
        }

        // Return unauthorized error if user is not authenticated
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
     * Store a newly created resource in storage.*/
    public function store(Request $request)
    {
        if (RoleHelper::ACSup()||RoleHelper::RespoLivraison()) {
            DatabaseHelper::Config();

            // Démarrer la transaction
            DB::connection('temp')->beginTransaction();

            try {
                $user = Auth::user();
                $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->first();

                if (!$userAuth) {
                    throw new \Exception('Utilisateur non trouvé');
                }

                $user_societes = User::where('id', $userAuth->user_id_origin)->first();
                $societe = Societe::findOrFail($user_societes->societe_id);

                // Création de la remise de clés
                $rec = new RemiseCle();
                $rec->setConnection('temp');
                $rec->bien_id = $request->bien_id;
                $rec->projet_id = $request->projet_id;
                $rec->date_remise = $request->date_remise;
                $rec->user_id = $userAuth->id;

                // Corrected logic for user_id_remis
                $rec->user_id_remis = $request->has('user_id_remise') && !empty($request->user_id_remise)
                    ? $request->user_id_remise
                    : $userAuth->id;

                // Gestion du fichier uploadé
                if ($request->hasFile('fichier')) {
                    $file = $request->file('fichier');
                    $fileName = $file->getClientOriginalName();

                    // MODIFICATION: Utiliser FichierHelper
                    FichierHelper::ajouter_fichier(
                        $file,
                        $societe->raison_sociale_concatene,
                        $societe->id,
                        'remise_cles',
                        $fileName
                    );
                    $rec->fichier = $fileName;
                }

                $rec->save();

                // NEW STATUT CLIENT
                $bien = Bien::on('temp')->with(['reservation.aquereurs.client', 'projet'])->find($request->bien_id);

                if ($bien && $bien->reservation) {
                    // Récupérer l'utilisateur qui a remis les clés
                    $userRemis = User::on('temp')->find($rec->user_id_remis);
                    $nomUserRemis = $userRemis ? ($userRemis->name . ' ' . ($userRemis->prenom ?? '')) : 'N/A';

                    // Pour chaque acquéreur de la réservation
                    foreach ($bien->reservation->aquereurs as $aquereur) {
                        if ($aquereur->client_id) {
                            $statutClient = new StatutClient();
                            $statutClient->setConnection('temp');

                            // Construction du commentaire
                            $comment = "Remise des clés effectuée ";

                            // Ajouter les informations du bien
                            if ($bien->propriete_dite_bien) {
                                $comment .= "- Bien: " . $bien->propriete_dite_bien . " ";
                            }

                            if ($bien->projet) {
                                $comment .= "- Projet: " . $bien->projet->nom . " ";
                            }

                            // Ajouter la date de remise
                            if ($request->date_remise) {
                                $comment .= "- Date: " . Carbon::parse($request->date_remise)->format('d/m/Y') . " ";
                            }

                            // Ajouter la personne qui a remis les clés
                            $comment .= "- Clés remises par: " . $nomUserRemis . " ";

                            // Si fichier uploadé
                            if ($request->hasFile('fichier')) {
                                $comment .= "- Document signé joint ";
                            }

                            // Ajouter la référence de réservation
                            if ($bien->reservation->code_reservation) {
                                $comment .= "- Réservation: " . $bien->reservation->code_reservation;
                            }

                            // Attribution des valeurs au StatutClient
                            $statutClient->client_id = $aquereur->client_id;
                            $statutClient->statut = 9; // Statut pour "Remise des clés"
                            $statutClient->reservation_id = $bien->reservation->id;
                            $statutClient->remise_cle_id = $rec->id;
                            $statutClient->date_traitement = Carbon::now();
                            $statutClient->user_id_traite = $userAuth->id;
                            $statutClient->commentaire = trim($comment);

                            $statutClient->save();
                        }
                    }
                }

                // Valider la transaction
                DB::connection('temp')->commit();

                return response()->json([
                    'remise' => $rec,
                    'message' => 'Remise des clés enregistrée avec succès'
                ], 200);

            } catch (\Exception $e) {
                // Annuler la transaction en cas d'erreur
                DB::connection('temp')->rollBack();

                // Journaliser l'erreur (optionnel)
                \Log::error('Erreur lors de la remise des clés: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'error' => 'Une erreur est survenue lors de l\'enregistrement',
                    'details' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (RoleHelper::ACSup()||RoleHelper::RespoLivraison()) {
            DatabaseHelper::Config();
            $remise = RemiseCle::on('temp')->findOrFail($id);
            return response()->json(['remise' => $remise], 200);
        }

        return response()->json(['error', 'Unauthorized'], 401);
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
    public function update(Request $request, $id)
    {
       if (RoleHelper::ACSup()||RoleHelper::RespoLivraison()) {
            DatabaseHelper::Config();
            $user          = Auth::user();
            $userAuth      = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
            $user_societes = User::where('id', $userAuth->value('user_id_origin'))->first();
            $societe       = Societe::findOrfail($user_societes->societe_id);
            $rec           = RemiseCle::on('temp')->findOrFail($id);
            $rec->setConnection('temp');
            $rec->date_remise   = $request->date_remise;
            $rec->user_id       = $userAuth->value('id');
            $rec->user_id_remis = $request->user_id_remise;
            $rec->bien_id       = $request->bien_id;
            $fich               = $rec->fichier;
            if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier s'il existe
            if ($fich != null) {
                FichierHelper::supprimer_fichier(
                    $societe->raison_sociale_concatene,
                    $societe->id,
                    'remise_cles',
                    $fich
                );
            }

            $file = $request->file('fichier');
            $fileName = $file->getClientOriginalName();

            // MODIFICATION: Utiliser FichierHelper
            FichierHelper::ajouter_fichier(
                $file,
                $societe->raison_sociale_concatene,
                $societe->id,
                'remise_cles',
                $fileName
            );
            $rec->fichier = $fileName;
        }
            $rec->save();
            return response()->json(['remise' => $rec], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (RoleHelper::ACSup()||RoleHelper::RespoLivraison()) {
            DatabaseHelper::Config();
            $user          = Auth::user();
            $userAuth      = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
            $user_societes = User::where('id', $userAuth->value('user_id_origin'))->first();
            $societe       = Societe::findOrfail($user_societes->societe_id);
            $rem           = RemiseCle::on('temp')->findOrFail($id);
            $fich          = $rem->fichier;
            if ($rem->delete()) {

               // MODIFICATION: Supprimer le fichier avec FichierHelper
            if ($fich != null) {
                FichierHelper::supprimer_fichier(
                    $societe->raison_sociale_concatene,
                    $societe->id,
                    'remise_cles',
                    $fich
                );
            }
                return response()->json(['message' => 'Remise supprimée avec succès.'], 200);
            } else {
                return response()->json(['error' => "La Remise n'a pas été supprimée."], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }


}
