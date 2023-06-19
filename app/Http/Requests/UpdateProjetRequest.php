<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'surface_terrain' => 'numeric',
            'prix_acquisition' => 'numeric',
            'limite_annulation_reservation' => 'integer',
            'nbr_tranches' => 'integer',
            'nbr_blocs' => 'integer',
            'nbr_immeubles' => 'integer',
            'nbr_biens' => 'integer'
            
        ];
    }
}
