<?php
// app/Http/Controllers/PDFController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\FichierHelper;
use Illuminate\Support\Facades\Storage;

class PDFController extends Controller
{
    public function generateCompromisPDF(Request $request)
    {
        try {
            $data = $request->input('data');

            if (!$data) {
                return response()->json(['error' => 'No data provided'], 400);
            }

            // Process logo if exists (supports both local and S3)
            $logoBase64 = null;
            if (isset($data['user']['societe']['logo']) &&
                isset($data['user']['societe']['raison_sociale_concatene']) &&
                isset($data['user']['societe']['id'])) {

                $societe = $data['user']['societe'];
                $logoFilename = $societe['logo'];
                $logoPath = $societe['raison_sociale_concatene'] . '_' . $societe['id'] . '/logos/' . $logoFilename;

                $fileContent = null;

                // Check if in production (S3) or local
                if (app()->environment('production')) {
                    // Get from S3
                    if (Storage::disk('s3')->exists($logoPath)) {
                        $fileContent = Storage::disk('s3')->get($logoPath);
                    }
                } else {
                    // Get from local public/docs
                    $localPath = public_path('docs/' . $logoPath);
                    if (file_exists($localPath)) {
                        $fileContent = file_get_contents($localPath);
                    }
                }

                if ($fileContent !== null) {
                    // Detect MIME type
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);

                    if (app()->environment('production')) {
                        // For S3, we can get from storage or detect from extension
                        $extension = pathinfo($logoFilename, PATHINFO_EXTENSION);
                        $mimeType = match($extension) {
                            'png' => 'image/png',
                            'jpg', 'jpeg' => 'image/jpeg',
                            'gif' => 'image/gif',
                            'svg' => 'image/svg+xml',
                            default => 'image/png'
                        };
                    } else {
                        $localPath = public_path('docs/' . $logoPath);
                        $mimeType = finfo_file($finfo, $localPath);
                        finfo_close($finfo);
                    }

                    $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
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

    /**
     * Alternative method that uses FichierHelper::get_file_url()
     * for PDF generation with remote images
     */
    public function generateCompromisPDFWithUrl(Request $request)
    {
        try {
            $data = $request->input('data');

            if (!$data) {
                return response()->json(['error' => 'No data provided'], 400);
            }

            // Process logo if exists using FichierHelper
            $logoUrl = null;
            if (isset($data['user']['societe']['logo']) &&
                isset($data['user']['societe']['raison_sociale_concatene']) &&
                isset($data['user']['societe']['id'])) {

                $societe = $data['user']['societe'];
                $logoUrl = FichierHelper::get_file_url(
                    $societe['raison_sociale_concatene'],
                    $societe['id'],
                    'logos',
                    $societe['logo']
                );
            }

            // Prepare all data for the view
            $pdfData = [
                'user' => $data['user'],
                'societe' => $data['user']['societe'] ?? null,
                'logoUrl' => $logoUrl,  // Use URL instead of base64
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

            // Enable remote URLs for PDF
            $pdf = Pdf::loadView('pdfs.compromis_vente_url', $pdfData);
            $pdf->setPaper('A4', 'portrait');

            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,  // Enable remote URLs for S3 images
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
