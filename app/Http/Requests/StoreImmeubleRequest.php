<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImmeubleRequest extends FormRequest
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
            'nom' => ['required', Rule::unique('immeubles')->where(function ($query) {
                $query->where('nom', $this->nom)
                    ->where('bloc_id', $this->bloc_id);})],
            'tranche_id' => 'integer',
            'projet_id' => 'required|integer',
            'nbre_biens' => 'integer',
            'bloc_id'=>'integer'
            
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'Cet immeuble est deje exist dans ce bloc',
        ];
    }
}
