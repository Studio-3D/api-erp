<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;
use App\Models\FacebookMessage;

class FacebookController extends Controller
{
   
   
        
 public function handleMessage(Request $request)
{
    Log::info($request);

    $messengerToken = 'messenger_token'; 
    if ($request->input('hub_verify_token') === $messengerToken) {
        return $request->input('hub_challenge');
    }

    $entries = $request->input('entry');

    if ($entries && is_array($entries)) {
        foreach ($entries as $entry) {
            $messaging = $entry['messaging'][0] ?? null;

            
            if ($messaging) {
                $senderId = $messaging['sender']['id'];
                $userProfile = $this->getUserProfile($senderId);
                $this->storeMessage($senderId, $userProfile['name'], $userProfile['id']);
                return response()->json(['status' => 'success']);
            }
        }
    }

    return response()->json(['status' => 'error', 'message' => 'Invalid request data']);
}

public function handleMessage_get(Request $request)
{
    $messengerToken = 'messenger_token'; 
    if ($request->input('hub_verify_token') === $messengerToken) {
        return $request->input('hub_challenge');
    }

    $entries = $request->input('entry');

    if ($entries && is_array($entries)) {
        foreach ($entries as $entry) {
            $messaging = $entry['messaging'][0] ?? null;

            if ($messaging) {
                $senderId = $messaging['sender']['id'];

                $userProfile = $this->getUserProfile($senderId);

                Log::info("Received message from {$userProfile['name']} (ID: $senderId): $messageText");

                $this->storeMessage($senderId, $userProfile['name'], $userProfile['id']);

                return response()->json(['status' => 'success']);
            }
        }
    }


    return response()->json(['status' => 'error', 'message' => 'Invalid request data']);
}
        private function getUserProfile($senderId)
        {
            $accessToken = 'EAAFQGIhFRKwBO4h6ypBIBESZC7ilV3ZApkZBj3iwMWZBfDrIbMsaYIsHCW5grHFKZBZAcWQ9JDNqtAg6GchKbT6GbZBQldyr6sAtCtCcVt0yHRxoRrotW6M3PVB6I3zVYhHGQBFfXhQgOdWtmgJ381tFFWGsqJ5T4OyiKlFlHC5TzbEdhE1MtFSFFtFIWvHU0LeL5l2ui32pbqi3r1pQ14aMKoZD'; // Replace with your Page Access Token
            $url = "https://graph.facebook.com/v18.0/$senderId?fields=id,name&access_token=$accessToken";
            $client = new Client();
            $response = $client->get($url);
    
            return json_decode($response->getBody(), true);
        }
    
        private function storeMessage($senderId, $userName,$userid)
        {
            FacebookMessage::create([
                'sender_id' => $senderId,
                'user_name' => $userName,
                'user_email' => $userid,
                'message' => 'message'
            ]);
        }

        public  function get_pivacy_policy()
        { 
            return 'this a test  of privacy  policy url';
        }
    }

