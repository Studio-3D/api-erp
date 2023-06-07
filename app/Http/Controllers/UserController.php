<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        
        if ($request->hasFile('photo')) {
            $logo = $request->file('photo');
            $logoPath = $logo->store('photos', 'public');
            $request['photo'] = $logoPath;
        }
        $user = User::create([
            'name' => $request['name'],
            'prenom' => $request['prenom'],
            'email' => $request['email'],
             'password' => Hash::make($request['password']),
             'gender' => $request['gender'],
            'type' => $request['type'],
            'phone' => $request['phone'],
             'photo' => $request['photo'],
            'cin' => $request['cin'],
            'fonction' => $request['fonction'],
            'date_embauche' => $request['date_embauche'],
            'niveau_etude' => $request['niveau_etude'],
            'adresse' => $request['adresse'],
            'cnss' => $request['cnss'],
           'is_actif' => $request['is_actif'],
            'nb_appel_recu' => $request['nb_appel_recu'],
            'nb_appel_traite' => $request['nb_appel_traite'],
            'sold_conge' => $request['sold_conge']
         ]);
        $token = $user->createToken('API Token')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {

            $accessToken = Auth::user()->createToken('API Token')->accessToken;
            return response()->json(['access_token' => $accessToken], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
