<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjetRequest extends FormRequest
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
            'date_autorisation_construction' => 'date',
            'date_permis_habiter' => 'date',
            'societe_id' => 'integer',
            'surface_terrain' => 'numeric',
            'prix_acquisition' => 'numeric',
            'limite_annulation_reservation' => 'integer',
            'nbr_tranches' => 'integer',
            'type_id' => 'integer',
            'nbr_blocs' => 'integer',
            'nbr_immeubles' => 'integer',
            'nbr_biens' => 'integer',
            'nom' => [ Rule::unique('projets')->where(function ($query) {
                $query->where('nom', $this->nom)
                    ->where('societe_id', $this->societe_id);})->ignore($this->projet)],
            
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'Ce projet est deja exist dans cette societe',
        ];
    }
}
