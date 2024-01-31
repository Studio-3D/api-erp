<?php

namespace App\Http\Controllers\WhatsApp;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; 
use App\Models\interfaceAPIs;
use App\Http\Controllers\ProspectController;
use Illuminate\Support\Facades\Auth;

class WhatsAppController extends Controller
{
    public function webhooks(Request $request)

    {
        Log::info($request);
        $whatsappToken = 'whatssap_token';
        if ($request->input('hub_verify_token') === $whatsappToken) {
            return $request->input('hub_challenge');
        }
        $entries = $request->input('entry');

        if ($entries && is_array($entries)) {
            foreach ($entries as $entry) {
                $messaging = $entry['changes'][0]['value']['messages'][0] ?? null;
                if ($messaging) {
                    $phone_number_id = $entry['changes'][0]['value']['metadata']['phone_number_id'];
                    $from = $messaging['from'];
                     $auth= Auth::guard('api')->user()->societe_id ?? 'societe_id not found ' ;
                    $msg_body = $messaging['text']['body'];
                    $name =$entry['changes'][0]['value']['contacts'][0]['profile']['name'];
                    $displayPhoneNumber = $entry['changes'][0]['value']['metadata']['display_phone_number'];
                    $this->storeMessage($displayPhoneNumber);
                    $interfaceAPI = interfaceAPIs::where('client_num', $displayPhoneNumber)->select('societe_id')->first();
                    $societe_id=$interfaceAPI->societe_id;
                    Log::info("Received message from $from (Phone ID: $phone_number_id): $msg_body  $societe_id ");
                    ProspectController::Store_WhatsApp($phone_number_id, $from, $msg_body,$name,$societe_id);  
                    Http::post("https://graph.facebook.com/v18.0/$phone_number_id/messages?access_token=''" , [
                        'messaging_product' => 'whatsapp',
                        'to' => $from,
                        'text' => ['body' => 'Ack: ' . $msg_body],
                    ]);
                    return response()->json(['status' => 'success']);
                }
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid request data']);
    }

    private function storeMessage($displayPhoneNumber)
    {
        interfaceAPIs::create([
           'client_num'=>$displayPhoneNumber,
           'societe_id'=>119,
           'source'=>1,
        ]);
    }

    
}

