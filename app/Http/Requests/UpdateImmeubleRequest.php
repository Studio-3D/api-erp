<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImmeubleRequest extends FormRequest
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
            'nom' => [ Rule::unique('immeubles')->where(function ($query) {
                $query->where('nom', $this->nom)
                    ->where('bloc_id', $this->bloc_id);})->ignore($this->immeuble)],
            'tranche_id' => 'integer',
            'projet_id' => 'integer',
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
