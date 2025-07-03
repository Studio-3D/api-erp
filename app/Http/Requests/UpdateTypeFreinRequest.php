<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Helpers\DatabaseHelper;
use App\Models\Societe;
use Illuminate\Support\Facades\Auth;

class UpdateTypeFreinRequest extends FormRequest
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
    $societe_id = Auth::guard('api')->user()->societe_id;
    $societe = Societe::findOrFail($societe_id);
    $DatabaseName = 'Erp_' . $societe->raison_sociale_concatene . '_' . $societe_id;
    DatabaseHelper::Config();

    $typeFrein = $this->route('typefrein'); // Récupère l'ID ou l'instance

    if (is_object($typeFrein)) {
        $idToIgnore = $typeFrein->id;
    } else {
        $idToIgnore = $typeFrein;
    }

    return [
        'description' => [
            'required',
            'min:3',
            Rule::unique('temp.' . $DatabaseName . '.type_freins', 'description')
                ->whereNull('deleted_at')
                ->ignore($idToIgnore),
        ],
    ];
}

public function messages(): array
{
    return [
        'description.required' => 'La description est obligatoire.',
        'description.min' => 'La description doit contenir au moins 3 caractères.',
        'description.unique' => 'Cette description existe déjà, veuillez en choisir une autre.',
    ];
}
}
