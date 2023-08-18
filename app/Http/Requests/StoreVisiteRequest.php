<?php

namespace App\Http\Requests;

use App\Http\Helpers\DatabaseHelper;
use App\Models\Societe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreVisiteRequest extends FormRequest
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
            'commentaire' => 'string|min:6',
            'source_id' => 'integer',
            'notifie' => 'boolean',
            'type_notification'=>'integer',
            'email'=>'string',
            'interet' => 'required|integer',
            'mode_relance' => 'integer',
            'date_relance' => 'date',
            'rdv' => 'datetime',
            'status' => 'string',
            'prospect_id'=>'required|integer',
            'bien_id'=>'integer',
        ];
    }
}
