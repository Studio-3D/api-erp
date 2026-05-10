<?php
// app/Http/Controllers/PDFController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    public function generateCompromisPDF(Request $request)
    {
        try {
            $data = $request->input('data');

            if (!$data) {
                return response()->json(['error' => 'No data provided'], 400);
            }

            // Process logo if exists
            $logoBase64 = null;
            if (isset($data['user']['societe']['logo']) &&
                isset($data['user']['societe']['raison_sociale_concatene']) &&
                isset($data['user']['societe']['id'])) {

                $logoPath = public_path("docs/{$data['user']['societe']['raison_sociale_concatene']}_{$data['user']['societe']['id']}/logos/{$data['user']['societe']['logo']}");

                if (file_exists($logoPath)) {
                    $fileContent = file_get_contents($logoPath);
                    if ($fileContent !== false) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $logoPath);
                        finfo_close($finfo);
                        $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
                    }
                }
            }

            // Prepare all data for the view
            $pdfData = [
                'user' => $data['user'],
                'societe' => $data['user']['societe'] ?? null,
                'logoBase64' => $logoBase64,
                'num_recu' => $data['num_recu'],
                'clients' => $data['clients'],
                'reservationDetails' => $data['reservationDetails'],
                'sum_avances_valides' => $data['sum_avances_valides'],
                'form' => $data['form'],
                'currentDate' => now()->format('d/m/Y'),
                'formatCivilite' => function($civilite) {
                    switch ($civilite) {
                        case "1": return "Monsieur";
                        case "2": return "Madame";
                        case "3": return "Mademoiselle";
                        default: return $civilite;
                    }
                }
            ];

            // Generate PDF
            $pdf = Pdf::loadView('pdfs.compromis_vente', $pdfData);
            $pdf->setPaper('A4', 'portrait');

            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isPhpEnabled' => true,
                'defaultFont' => 'dejavu sans',
                'chroot' => public_path(),
            ]);

            return $pdf->download("compromis_vente_{$data['num_recu']}.pdf");

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }
}
