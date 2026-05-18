<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Avance;
use App\Models\Societe;
use App\Models\PiecesJointe;
use Illuminate\Http\Request;
use App\Http\Helpers\RoleHelper;
use App\Http\Helpers\DatabaseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePiecesJointeRequest;
use App\Http\Requests\UpdatePiecesJointeRequest;
use App\Http\Helpers\FichierHelper;  // AJOUTER CETTE LIGNE

class PiecesJointeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $projet_id)
    {
        if (Auth::guard('api')->check()) {
            DatabaseHelper::Config();
            $perPage = $request->input('pageSize', 5);
            $page = $request->input('page', 1);
            $pjs = PiecesJointe::on('temp')
                ->join('reservations', 'reservations.projet_id', '=', 'aqeureurs.reservation_id')
                ->whereNull('reservations.deleted_at')
                ->join('projets', 'reservations.projet_id', '=', 'projets.id')
                ->where('projets.id', $projet_id)
                ->select('pieces_jointes.*')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], $page);
            return response()->json(['PJs' => $pjs], 200);
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
    public function store(StorePiecesJointeRequest $request)
    {
        if (RoleHelper::ACSup()  || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pJ = new PiecesJointe();
            $pJ->setConnection('temp');
            $pJ->fichier = $request->fichier;
            $pJ->type = $request->type;
            $pJ->avance_id = $request->avance_id;
            $pJ->reservation_id = $request->reservation_id;
            $pJ->desistement_id = $request->desistement_id;
            $pJ->penalite_id = $request->penalite_id;
            $pJ->reclamation_id = $request->reclamation_id;
            $pJ->active = $request->active;
            if($request->pj_scanner){
                $pJ->pj_scanner = $request->pj_scanner;
            }

            if ($pJ->save()) {
                return response()->json(['PJ' => $pJ], 200);
            } else {
                return response()->json(['error' => 'Échec de la sauvegarde de la PJ'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (RoleHelper::ACSup() || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pJ = PiecesJointe::on('temp')->findOrFail($id);
            return response()->json(['pJ' => $pJ], 200);
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
    public function update(UpdatePiecesJointeRequest $request, $id)
    {
        if (RoleHelper::ACSup() || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pJ = PiecesJointe::on('temp')->findOrFail($id);

            if ($request->hasFile('fichier')) {
                $file = time() . '.' . $request->file('fichier')->getClientOriginalName();
                $request->file("fichier")->move(public_path('img/fichier'), $file);
                $pJ->fichier = $file;
                $pJ->type = $request->file('fichier')->getClientOriginalExtension();
                $pJ->avance_id = $request->input("avance_id");
                $pJ->reservation_id = $request->input("reservation_id");
                $pJ->reclamation_id = $request->input("reclamation_id");
            }

            if ($pJ->save()) {
                return response()->json(["pJ" => $pJ], 200);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (RoleHelper::ACSup()  || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pj = PiecesJointe::on('temp')->findOrFail($id);
            if ($pj->delete()) {
                return response()->json(['message' => 'PJ deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'PJ non deleted '], 400);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function soft_destroy_pj_by_reservationId($reservation_id)
    {
        if (RoleHelper::ACSup() || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pj = PiecesJointe::on('temp')->where('reservation_id', $reservation_id)->get();
            foreach ($pj as $p) {
                $p->delete();
            }
            return response()->json(['message' => 'Piéce Jointe supprimés avec succès'], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function soft_destroy_pj_by_penalite_id($penalite_id)
    {
        if (RoleHelper::ACSup() || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pj = PiecesJointe::on('temp')->where('penalite_id', $penalite_id)->get();
            foreach ($pj as $p) {
                $p->delete();
            }
            return response()->json(['message' => 'Piéce Jointe supprimés avec succès'], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function getFileUsingReservationId($reservation_id)
    {
        if (RoleHelper::ACSup()  || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pj = PiecesJointe::on('temp')->where('reservation_id', $reservation_id)->get();
            if ($pj->isEmpty()) {
                return response()->json(['message' => 'Aucune PJ dans cette reservation'], 400);
            } else {
                return response()->json(['pJ' => $pj], 200);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function getFilesUsingReservationId($reservation_id,$societe)
{
    if (RoleHelper::ACSup()  || RoleHelper::AgentAdmin()) {
        DatabaseHelper::Config();

        // Get all pieces jointes for this reservation
        $piecesJointes = PiecesJointe::on('temp')
            ->where('reservation_id', $reservation_id)
            ->get();

        // Transform the collection to include file existence info
        $files = $piecesJointes->map(function($pj) use ($societe) {
            return [
                'id' => $pj->id,
                'fichier' => $pj->fichier,
                'type' => $pj->type,
                'created_at' => $pj->created_at,
                'active' => $pj->active,
            ];
        });

        return $files;
    }

    return response()->json(['error' => 'Unauthorized'], 401);
}
    public function destoryFileUsingReservationId($reservation_id,$code_reservation,$societe)
    {
        if (RoleHelper::ACSup() || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pj = PiecesJointe::on('temp')->where('reservation_id', $reservation_id)->get();
            foreach ($pj as $p) {

                // MODIFICATION: Utiliser FichierHelper
                FichierHelper::supprimer_fichier(
                    $societe->raison_sociale_concatene,
                    $societe->id,
                    'reservations/' . $code_reservation,
                    $p->fichier
                );
                $p->delete();
            }
            return response()->json(['message' => 'PJ deleted successfully'], 200);

        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function destoryFileUsingAvanceId($avance_id,$societe)
    {
        if (RoleHelper::ACSup()  || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $avance=Avance::on('temp')->findOrfail($avance_id);
            $pj = PiecesJointe::on('temp')->where('avance_id', $avance_id)->get();
            foreach ($pj as $p) {

                // MODIFICATION: Utiliser FichierHelper
                FichierHelper::supprimer_fichier(
                    $societe->raison_sociale_concatene,
                    $societe->id,
                    'paiements/' . $avance->reservation->code_reservation,
                    $p->fichier
                );
                $p->delete();
            }
            return response()->json(['message' => 'PJ deleted successfully'], 200);

        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function destoryFileUsingReclamationId($rec_id,$societe)
    {
        if (RoleHelper::ACSup()  || RoleHelper::AgentAdmin()) {
            DatabaseHelper::Config();
            $pj = PiecesJointe::on('temp')->where('reclamation_id', $rec_id)->get();
            foreach ($pj as $p) {

                 // MODIFICATION: Utiliser FichierHelper
            FichierHelper::supprimer_fichier(
                $societe->raison_sociale_concatene,
                $societe->id,
                'reclamations',
                $p->fichier
            );
            $p->delete();
            }
            return response()->json(['message' => 'PJ deleted successfully'], 200);

        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function scanner_file(Request $request)
        {
            if (RoleHelper::ACSup() || RoleHelper::AgentAdmin()) {
                DatabaseHelper::Config();
                $user = Auth::user();
                $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
                if ($request->hasFile('fichier_scanner')) {

                    $user_societes = User::where('id', $userAuth->value('user_id_origin'))->first();
                    $societe = Societe::findOrfail($user_societes->societe_id);
                    $avance = Avance::on('temp')->findOrfail($request->input("avance_id"));
                    $avance->setConnection('temp');

                    // Récupérer le nom du fichier
                    $file = $request->file('fichier_scanner');
                    $fileName = $file->getClientOriginalName();

                    // MODIFICATION: Utiliser FichierHelper
                    FichierHelper::ajouter_fichier(
                        $file,
                        $societe->raison_sociale_concatene,
                        $societe->id,
                        'paiements/' . $avance->reservation->code_reservation,
                        $fileName
                    );

                    $avance->recu_scanne = $fileName;

                    if (!$avance->save()) {
                        return response()->json(['error' => 'Échec de scanner les fichiers'], 500);
                    }
                }

                return response()->json(['success' => 'Fichiers scannés avec succès'], 200);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
   public function files_docs($doss)
        {
            $user = Auth::user();
            DatabaseHelper::Config();
            $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
            $user_connecter = $userAuth->value('user_id_origin');
            $user_societes = User::where('id', $user_connecter)->first();
            $societe = Societe::findOrfail($user_societes->societe_id);

            // Déterminer le sous-dossier en fonction de la variable $doss
            $subdirectory = $doss == 'rsv' ? 'reservations' : ($doss == 'avc' ? 'paiements' : ($doss == 'plt' ? 'penalites' : 'desistement'));

            // Chemin relatif
            $relativePath = $societe->raison_sociale_concatene . '_' . $societe->id . '/' . $subdirectory;

            if (app()->environment('production')) {
                // Pour S3
                $files = Storage::disk('s3')->files($relativePath);
                $files = array_map(function($file) {
                    return basename($file);
                }, $files);
            } else {
                // Pour local
                $directory = public_path('docs/' . $relativePath);
                if (!is_dir($directory)) {
                    return response()->json([]);
                }
                $files = scandir($directory);
                $files = array_diff($files, ['.', '..']);
            }

            return response()->json(array_values($files));
        }

    public function files_docs_by_code($doss, $code)
{
    $user = Auth::user();
    DatabaseHelper::Config();
    $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
    $user_connecter = $userAuth->value('user_id_origin');
    $user_societes = User::where('id', $user_connecter)->first();
    $societe = Societe::findOrfail($user_societes->societe_id);

    // Déterminer le sous-dossier en fonction de la variable $doss
    $subdirectory = $doss == 'rsv' ? 'reservations' : ($doss == 'avc' ? 'paiements' : ($doss == 'plt' ? 'penalites' : 'desistement'));

    // Chemin relatif
    $relativePath = $societe->raison_sociale_concatene . '_' . $societe->id . '/' . $subdirectory . '/' . $code;

    if (app()->environment('production')) {
        // Pour S3
        $files = Storage::disk('s3')->files($relativePath);
        $files = array_map(function($file) {
            return basename($file);
        }, $files);
    } else {
        // Pour local
        $directory = public_path('docs/' . $relativePath);
        if (!is_dir($directory)) {
            return response()->json([]);
        }
        $files = scandir($directory);
        $files = array_diff($files, ['.', '..']);
    }

    return response()->json(array_values($files));
}
}
