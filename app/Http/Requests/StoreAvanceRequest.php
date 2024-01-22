<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreAvanceRequest extends FormRequest
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
        $rules['montant']='required';
        $rules['mode_paiement']='required';
         //mode_paiement cheqyue/cheque_banque/cheque_certifie/
         if ($request->mode_paiement == 2||$request->mode_paiement == 3||$request->mode_paiement == 4){
            $rules['banque_id']='required';
            $rules['numero_paiement']='required';
            $rules['echeance']='required';
        }
        //virement versement
        elseif ($request->mode_paiement == 5||$request->mode_paiement == 6){
            $rules['banque_id']='required';
            $rules['numero_paiement']='required';

        }

        return $rules;


    }
}
