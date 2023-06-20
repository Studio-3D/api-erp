<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlocRequest extends FormRequest
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
            'titre_foncier' => 'required',
            'projet_id' => 'required|integer',
            'tranche_id' => 'integer',
            'nbre_immeubles' => 'integer',
            'nbre_biens' => 'integer',
            'nom' => ['required', Rule::unique('blocs')->where(function ($query) {
                $query->where('nom', $this->nom)
                    ->where('tranche_id', $this->tranche_id);})],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'Ce bloc est deje exist dans ce tranche',
        ];
    }
}
