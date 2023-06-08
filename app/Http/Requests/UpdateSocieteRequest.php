<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocieteRequest extends FormRequest
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
            'raison_sociale' => 'required',
           /*  'raison_sociale' => [
                'required',
                Rule::unique('societes')->ignore($this->id)], */
            'nom_contact' => 'required',
            'tel' => 'string|size:14',
            'email' => 'email',
            'logo' => 'image',
        
        ];
    }
}
