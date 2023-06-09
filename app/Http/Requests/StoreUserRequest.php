<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'type' => 'required|string',
            'phone' => 'size:10',
            'photo' => 'image',
            'cin' => 'string|unique:users',
            'date_embauche' => 'date',
            'cnss' => 'integer',
            'is_actif' => 'integer',
            'nb_appel_recu' => 'integer',
            'nb_appel_traite' => 'integer',
            'sold_conge' => 'integer', 
        
            //
        ];
    }
}
