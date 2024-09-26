<?php

namespace App\Http\Requests;


use App\Http\Helpers\DatabaseHelper;
use App\Models\Societe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

#[AllowDynamicProperties] class StoreFactureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(Request $request): array
    {
        $rules = [];
        $rules['fournisseur_id']='required';
        $rules['decompte_id']='required';
        $rules['date_facture']='required';
        $rules['num_facture']='required';
        $rules['piece_jointe']='required';
        $rules['ht']='required';
        $rules['taux_tva']='required';
        $rules['tva']='required';
        $rules['retenue_garantie']='required';
        $rules['ttc']='required';
        $rules['montant']='required';
        $rules['date_paiement']='required';
        $rules['mode_paiement']='required';
         //mode_paiement cheqyue/cheque_banque/cheque_certifie/
         if ($request->mode_paiement == 2||$request->mode_paiement == 3||$request->mode_paiement == 4){
            $rules['banque_id']='required';
            $rules['numero_paiement']='required';
            $rules['date_echeance']='required';
        }
        //virement versement
        elseif ($request->mode_paiement == 5||$request->mode_paiement == 6){
            $rules['banque_id']='required';
            $rules['numero_paiement']='required';

        }
        return $rules;


    }
}
