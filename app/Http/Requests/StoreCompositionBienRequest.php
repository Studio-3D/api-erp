<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompositionBienRequest extends FormRequest
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
            'bien_id' => 'required|integer',
            'nbre_chambres' => 'integer',
            'nbre_salons' => 'integer',
            'nbre_sdb' => 'integer',
            'nbre_cuisines' => 'integer',
            'nbre_halls' => 'integer',
            'nbre_terasses' => 'integer',
            'nbre_balcons' => 'integer',
            'nbre_buanderies' => 'integer',
            'nbre_placards' => 'integer',
            'nbre_receptions' => 'integer',       

        ];
    }
}
