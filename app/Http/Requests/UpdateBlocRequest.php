<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlocRequest extends FormRequest
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
            'tranche_id' => 'integer',
            'nbre_immeubles' => 'integer',
            'nbre_biens' => 'integer',
            'nom' => [ Rule::unique('blocs')->where(function ($query) {
                if ($this->tranche_id==null){
                    $query->where('nom', $this->nom)
                    ->where('projet_id', $this->projet_id);
                }
                else {
                    $query->where('nom', $this->nom)
                    ->where('tranche_id', $this->tranche_id);
                }

                
                
            })->ignore($this->bloc)],
            
        ];
    }

    public function messages(): array
    {   if ($this->tranche_id==null){
            return [
            
            'nom.unique' =>  'Ce bloc est deja exist dans ce projet',
            ];
        }

        else {
            return [
                
                'nom.unique' =>  'Ce bloc est deja exist dans ce tranche',
            ];
        }
    }
}
