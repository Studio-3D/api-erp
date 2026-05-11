<?php
// app/Http/Controllers/WhatsAppWebhookController.php

namespace App\Http\Controllers\WhatsApp;

use Illuminate\Http\Request;
use Twilio\TwiML\MessagingResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Gérer les messages entrants de WhatsApp
     */
    public function handleIncoming(Request $request)
    {
        // Récupérer les données du message
        // Nettoyer les données
        $from = str_replace('whatsapp:', '', $request->input('From'));
        $body = trim($request->input('Body'));

        // Log simple
         Log::info("📱 WhatsApp de $from: $body");
        // Créer une réponse automatique simple
        $response = new MessagingResponse();
        $response->message("Merci pour votre message ! Nous vous répondrons bientôt.");

        return response($response, 200)
            ->header('Content-Type', 'text/xml');
    }
}
