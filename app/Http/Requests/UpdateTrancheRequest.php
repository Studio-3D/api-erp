<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrancheRequest extends FormRequest
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
            'projet_id' => 'integer',
            'date_lancement' => 'date',
            'date_livraison' => 'date',
            'niveau_etages' => 'integer',
            'nbre_blocs' => 'integer ',
            'nbre_immeubles' => 'integer',
            'nbre_biens' => 'integer',
            'nom' => [Rule::unique('tranches')->where(function ($query) {
                $query->where('nom', $this->nom)
                    ->where('projet_id', $this->projet_id);})->ignore($this->tranche)],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'ce tranche est deje exist dans ce projet',
        ];
    }
}
