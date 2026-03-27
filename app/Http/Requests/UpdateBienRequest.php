<?php

namespace App\Http\Requests;

use App\Http\Helpers\DatabaseHelper;
use App\Models\Societe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateBienRequest extends FormRequest
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
        $societe = Societe::findOrfail($societe_id);
        $DatabaseName = 'Erp_' . $societe->raison_sociale_concatene . '_' . $societe_id;
        DatabaseHelper::Config();

        // Règles de base
        $rules = [
            'numero' => ['string', Rule::unique('temp.' . $DatabaseName . '.biens', 'numero')->whereNull('deleted_at')->where(function ($query) {
                if ($this->immeuble_id == null) {
                    if ($this->bloc_id == null) {
                        if ($this->tranche_id == null) {
                            $query->where('numero', $this->numero)
                                ->where('projet_id', $this->projet_id);
                        } else {
                            $query->where('numero', $this->numero)
                                ->where('tranche_id', $this->tranche_id);
                        }
                    } else {
                        $query->where('numero', $this->numero)
                            ->where('bloc_id', $this->bloc_id);
                    }
                } else {
                    $query->where('numero', $this->numero)
                        ->where('immeuble_id', $this->immeuble_id);
                }
            })->ignore($this->bien)],
            'prix_unitaire' => 'numeric',
            'avance_minimale' => 'numeric',
            'prix' => 'numeric',
            'superficie_habitable' => 'numeric|nullable',
            'nbre_facades' => 'integer',
            'superficie_parking' => 'numeric|nullable',
            'superficie_architecte' => 'numeric',
            'superficie_box' => 'numeric|nullable',
            'superficie_terrasse' => 'numeric|nullable',
            'superficie_jardin' => 'numeric|nullable',
            'type_id' => 'integer',
            'projet_id' => 'integer',
            'tranche_id' => 'integer|nullable',
            'bloc_id' => 'integer|nullable',
            'immeuble_id' => 'integer|nullable',
            'propriete_dite_bien' => ['string', Rule::unique('temp.' . $DatabaseName . '.biens', 'propriete_dite_bien')->whereNull('deleted_at')->where(function ($query) {
                if ($this->immeuble_id == null) {
                    if ($this->bloc_id == null) {
                        if ($this->tranche_id == null) {
                            $query->where('propriete_dite_bien', $this->propriete_dite_bien)
                                ->where('projet_id', $this->projet_id);
                        } else {
                            $query->where('propriete_dite_bien', $this->propriete_dite_bien)
                                ->where('tranche_id', $this->tranche_id);
                        }
                    } else {
                        $query->where('propriete_dite_bien', $this->propriete_dite_bien)
                            ->where('bloc_id', $this->bloc_id);
                    }
                } else {
                    $query->where('propriete_dite_bien', $this->propriete_dite_bien)
                        ->where('immeuble_id', $this->immeuble_id);
                }
            })->ignore($this->bien)],
            'vue_id' => 'integer|nullable',
            'typologie_id' => 'integer|nullable',
        ];

        // Règles conditionnelles pour le champ niveau
        if ($this->bloc_id || $this->tranche_id || $this->immeuble_id) {
            // Si un des IDs parent est présent, niveau est requis mais peut être null ou vide
            $rules['niveau'] = 'required|integer|nullable';
        } else {
            // Sinon, niveau est optionnel
            $rules['niveau'] = 'nullable';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            // Messages pour le champ numero
            'numero.string' => 'Le numéro du bien doit être une chaîne de caractères',

            // Messages pour le champ prix_unitaire
            'prix_unitaire.numeric' => 'Le prix unitaire doit être un nombre',

            // Messages pour le champ avance_minimale
            'avance_minimale.numeric' => 'L\'avance minimale doit être un nombre',

            // Messages pour le champ prix
            'prix.numeric' => 'Le prix doit être un nombre',

            // Messages pour le champ superficie_habitable
            'superficie_habitable.numeric' => 'La superficie habitable doit être un nombre',

            // Messages pour le champ nbre_facades
            'nbre_facades.integer' => 'Le nombre de façades doit être un nombre entier',

            // Messages pour le champ superficie_parking
            'superficie_parking.numeric' => 'La superficie du parking doit être un nombre',

            // Messages pour le champ superficie_architecte
            'superficie_architecte.numeric' => 'La superficie architecte doit être un nombre',

            // Messages pour le champ superficie_box
            'superficie_box.numeric' => 'La superficie du box doit être un nombre',

            // Messages pour le champ superficie_terrasse
            'superficie_terrasse.numeric' => 'La superficie de la terrasse doit être un nombre',

            // Messages pour le champ superficie_jardin
            'superficie_jardin.numeric' => 'La superficie du jardin doit être un nombre',

            // Messages pour le champ type_id
            'type_id.integer' => 'Le type de bien doit être un identifiant valide',

            // Messages pour le champ projet_id
            'projet_id.integer' => 'Le projet doit être un identifiant valide',

            // Messages pour le champ tranche_id
            'tranche_id.integer' => 'La tranche doit être un identifiant valide',

            // Messages pour le champ bloc_id
            'bloc_id.integer' => 'Le bloc doit être un identifiant valide',

            // Messages pour le champ immeuble_id
            'immeuble_id.integer' => 'L\'immeuble doit être un identifiant valide',

            // Messages pour le champ propriete_dite_bien
            'propriete_dite_bien.string' => 'La propriété dite bien doit être une chaîne de caractères',

            // Messages pour le champ vue_id
            'vue_id.integer' => 'La vue doit être un identifiant valide',

            // Messages pour le champ typologie_id
            'typologie_id.integer' => 'La typologie doit être un identifiant valide',
        ];

        // Messages conditionnels pour numero.unique selon le contexte
        if ($this->tranche_id == null && $this->bloc_id == null && $this->immeuble_id == null) {
            $messages['numero.unique'] = 'Ce numéro de bien existe déjà dans ce projet';
        } elseif ($this->immeuble_id == null && $this->bloc_id == null && $this->tranche_id != null) {
            $messages['numero.unique'] = 'Ce numéro de bien existe déjà dans cette tranche';
        } elseif ($this->immeuble_id == null && $this->bloc_id != null) {
            $messages['numero.unique'] = 'Ce numéro de bien existe déjà dans ce bloc';
        } elseif ($this->immeuble_id != null) {
            $messages['numero.unique'] = 'Ce numéro de bien existe déjà dans cet immeuble';
        }

        // Messages conditionnels pour propriete_dite_bien.unique selon le contexte
        if ($this->tranche_id == null && $this->bloc_id == null && $this->immeuble_id == null) {
            $messages['propriete_dite_bien.unique'] = 'Cette propriété dite bien existe déjà dans ce projet';
        } elseif ($this->immeuble_id == null && $this->bloc_id == null && $this->tranche_id != null) {
            $messages['propriete_dite_bien.unique'] = 'Cette propriété dite bien existe déjà dans cette tranche';
        } elseif ($this->immeuble_id == null && $this->bloc_id != null) {
            $messages['propriete_dite_bien.unique'] = 'Cette propriété dite bien existe déjà dans ce bloc';
        } elseif ($this->immeuble_id != null) {
            $messages['propriete_dite_bien.unique'] = 'Cette propriété dite bien existe déjà dans cet immeuble';
        }

        // Messages conditionnels pour le champ niveau
        if ($this->bloc_id || $this->tranche_id || $this->immeuble_id) {
            $messages['niveau.required'] = 'Le niveau (étage) est requis pour ce type de bien';
            $messages['niveau.integer'] = 'Le niveau doit être un nombre entier';
        }

        return $messages;
    }
}
