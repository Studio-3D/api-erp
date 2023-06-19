<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjetRequest extends FormRequest
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
            'code' => 'required|string',
            'adresse' => 'required|string',
            'date_autorisation_construction' => 'required|date',
            'date_permis_habiter' => 'required|date',
            'titre_foncier' => 'required|string',
            'surface_terrain' => 'required|numeric',
            'prix_acquisition' => 'required|numeric',
            'limite_annulation_reservation' => 'required|integer',
            'type_id' => 'required|integer',
            'nbr_tranches' => 'integer',
            'nbr_blocs' => 'integer',
            'nbr_immeubles' => 'integer',
            'nbr_biens' => 'integer',
            'societe_id' => 'required|integer',
            'nom' => ['required', Rule::unique('projets')->where(function ($query) {
                $query->where('nom', $this->nom)
                    ->where('societe_id', $this->societe_id);})],

        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'Ce projet est deje exist dans cette societe',
        ];
    }
}
