<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
        return [
            'niveau' => 'integer',
            'prix_unitaire' => 'numeric',
            'prix' => 'numeric',
            'superficie_habitable' => 'numeric',
            'nbre_facades' => 'integer',
            'superficie_parking' => 'numeric',
            'superficie_architecte' => 'numeric',
            'superficie_box' => 'numeric',
            'superficie_terrasse' => 'numeric',
            'superficie_jardin' => 'numeric',
            'type_id' => 'integer',
            'projet_id' => 'integer',
            'tranche_id' => 'integer',
            'bloc_id' => 'integer',
            'immeuble_id' => 'integer',
            'propriete_dite_bien' => [ Rule::unique('biens')->where(function ($query) {
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
                        })->ignore($this->bien)],
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
