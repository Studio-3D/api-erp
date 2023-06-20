<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBienRequest extends FormRequest
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
            'numero' => 'required',
            'niveau' => 'required|integer',
            'orientation' => 'required',
            'conventionne' => 'required',
            'prix_unitaire' => 'required|numeric',
            'prix' => 'required|numeric',
            'superficie_habitable' => 'required|numeric',
            'superficie_architecte' => 'required|numeric',
            'nbre_facades' => 'required|integer',
            'superficie_parking' => 'required|numeric',
            'superficie_box' => 'required|numeric',
            'superficie_terrasse' => 'required|numeric',
            'superficie_jardin' => 'required|numeric',
            'titre_foncier' => 'required',
            'etat' => 'required',
            'type_id' => 'required|integer',
            'projet_id' => 'required|integer',
            'tranche_id' => 'integer',
            'bloc_id' => 'integer',
            'immeuble_id' => 'integer',
            'propriete_dite_bien' => ['required', Rule::unique('biens')->where(function ($query) {
                        if ($this->immeuble_id==null){
                            if ($this->bloc_id==null){
                                if ($this->tranche_id==null){
                                    $query->where('propriete_dite_bien', $this->propriete_dite_bien)
                                    ->where('projet_id', $this->projet_id);
                                }
                                else {$query->where('propriete_dite_bien', $this->propriete_dite_bien)
                                    ->where('tranche_id', $this->tranche_id);}

                                }
                            else{
                                $query->where('propriete_dite_bien', $this->propriete_dite_bien)
                                ->where('bloc_id', $this->bloc_id);
                            }
                        }
                        else {$query->where('propriete_dite_bien', $this->propriete_dite_bien)
                                ->where('immeuble_id', $this->immeuble_id);   
                        }         
                        })],
         
            
        ];
    }

    public function messages(): array

        {   if ($this->tranche_id==null && $this->bloc_id==null && $this->immeuble_id==null){
                return [
            
                'propriete_dite_bien.unique' =>  'Ce bien est deja exist dans ce projet',
            ];
            }

            elseif ($this->immeuble_id==null && $this->bloc_id==null) {
                return [
                
                'propriete_dite_bien.unique' =>  'Ce bien est deja exist dans ce tranche',
            ];
            }
            elseif ($this->immeuble_id==null ) {
            return [
                
                'propriete_dite_bien.unique' =>  'Ce bien est deja exist dans ce bloc',
            ];
            }

            else {
                return [
                    
                    'propriete_dite_bien.unique' =>  'Ce bien est deja exist dans cet emmeuble',
                ];
                }
        }
}


