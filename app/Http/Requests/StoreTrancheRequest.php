<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrancheRequest extends FormRequest
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
    public function rules(): array
    {
        return [

            'nom' => ['required', Rule::unique('tranches')->where(function ($query) {
                $query->where('nom', $this->nom)
                    ->where('projet_id', $this->projet_id);})],
            'date_lancement' => 'required|date',
            'date_livraison' => 'required|date',
            'niveau_etages' => 'required|integer',
            'nbre_blocs' => 'integer ',
            'nbre_immeubles' => 'integer',
            'nbre_biens' => 'integer',
            'projet_id'=>'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'Ce tranche est deje exist dans ce projet',
        ];
    }
}
