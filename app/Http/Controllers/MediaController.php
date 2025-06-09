<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaController extends Controller
{
    /**
     * Serve the media file with caching controls to prevent redirection issues
     *
     * @param string $path The path to the file in storage
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    public function show($path)
    {
        $fullPath = $path;
        
        // Check if file exists
        if (!Storage::disk('public')->exists($fullPath)) {
            throw new NotFoundHttpException('File not found: ' . $fullPath);
        }
        
        $mimetype = Storage::disk('public')->mimeType($fullPath);
        $filename = basename($fullPath);
        
        // Return file with appropriate headers for better handling by social media sites
        $headers = [
            'Content-Type' => $mimetype,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=31536000', // Longer cache for better performance
            'Accept-Ranges' => 'bytes',
            'Access-Control-Allow-Origin' => '*',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Length' => Storage::disk('public')->size($fullPath),
            'Pragma' => 'public', // For older browsers
        ];

        return Storage::disk('public')->download($fullPath, $filename, $headers);
    }
}
