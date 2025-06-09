<?php

namespace App\Http\Controllers\TikTok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\RequestException;

class TikTokApiController extends Controller
{
    protected $clientToken;
    protected $clientSecret;
    protected $apiBaseUrl;
    
    public function __construct()
    {
        $this->clientToken = env('TIKTOK_CLIENT_TOKEN', '');
        $this->clientSecret = env('TIKTOK_CLIENT_SECRET', '');
        $this->apiBaseUrl = env('TIKTOK_API_URL', 'https://open.tiktokapis.com/v2');
    }
    
    /**
     * Publish content to TikTok
     */
    public function publishContent(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:150',
                'description' => 'required|string|max:2200',
                'media_url' => 'required|url',
                'media_type' => 'required|in:PHOTO,VIDEO',
            ]);
            
            // Generate a unique ID for this publish request
            $publishId = Str::uuid()->toString();
            
            // Log the publish attempt
            Log::info("TikTok publish attempt", [
                'publish_id' => $publishId,
                'media_type' => $request->media_type,
                'media_url' => $request->media_url,
            ]);
            
            // Check if we're in mock mode (default is true)
            if (env('TIKTOK_MOCK_MODE', true)) {
                // Return a mock success response
                return response()->json([
                    'success' => true,
                    'message' => 'Content queued for publishing to TikTok',
                    'publish_id' => $publishId,
                    'status' => 'PUBLISH_PROCESSING'
                ]);
            } else {
                // Initialize the HTTP client for the real TikTok API
                $client = new Client([
                    'base_uri' => $this->apiBaseUrl,
                    'timeout' => 30,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->clientToken,
                        'Content-Type' => 'application/json',
                    ]
                ]);
                
                // Updated endpoint and payload format for TikTok API v2
                $endpoint = '/post/publish/content/init/';
                
                // Prepare the request payload for the TikTok API
                $payload = [
                    'post_info' => [
                        'title' => $request->title,
                        'description' => $request->description,
                        'visibility' => 'PUBLIC',
                    ],
                    'media_info' => [
                        'source_info' => [
                            'source' => 'PULL_FROM_URL',
                            'media_url' => $request->media_url,
                            'media_type' => $request->media_type,
                        ]
                    ],
                    'publish_mode' => 'STANDARD',
                ];
                
                try {
                    $response = $client->post($endpoint, [
                        'json' => $payload
                    ]);
                    
                    $responseData = json_decode($response->getBody(), true);
                    
                    // Process the real API response
                    Log::info('TikTok API Response', ['response' => $responseData]);
                    
                    if (isset($responseData['data']['publish_id'])) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Content submitted to TikTok API',
                            'publish_id' => $responseData['data']['publish_id'],
                            'status' => 'PUBLISH_PROCESSING'
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid response from TikTok API',
                            'response' => $responseData
                        ], 500);
                    }
                } catch (RequestException $e) {
                    Log::error('TikTok API Error', [
                        'error' => $e->getMessage(),
                        'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Error communicating with TikTok: ' . $e->getMessage()
                    ], 500);
                }
            }
        } catch (\Exception $e) {
            Log::error("TikTok publish error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish to TikTok: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check the status of a publishing request
     */
    public function checkPublishStatus(Request $request)
    {
        $request->validate([
            'publish_id' => 'required|string'
        ]);
        
        $publishId = $request->input('publish_id');
        
        try {
            // Log the status check attempt
            Log::info("TikTok status check for publish_id: $publishId");
            
            // Check if we're in mock mode (default is true)
            if (env('TIKTOK_MOCK_MODE', true)) {
                // Include realistic response data for mock
                $contentId = substr(str_shuffle('0123456789'), 0, 10);
                $username = env('TIKTOK_USERNAME', 'yourbusiness');
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'publish_id' => $publishId,
                        'data' => [
                            'publish_status' => 'PUBLISH_COMPLETE',
                            'tiktok_post_url' => "https://www.tiktok.com/@$username/video/$contentId",
                            'content_id' => $contentId,
                            'timestamp' => now()->toIso8601String()
                        ]
                    ]
                ]);
            } else {
                // Initialize the HTTP client for the real TikTok API
                $client = new Client([
                    'base_uri' => $this->apiBaseUrl,
                    'timeout' => 30,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->clientToken,
                        'Content-Type' => 'application/json',
                    ]
                ]);
                
                try {
                    // Updated endpoint for TikTok API v2
                    $response = $client->get('/post/publish/status/query/', [
                        'query' => ['publish_id' => $publishId]
                    ]);
                    
                    $responseData = json_decode($response->getBody(), true);
                    
                    // Process the real API response
                    Log::info('TikTok Status Check Response', ['response' => $responseData]);
                    
                    return response()->json([
                        'success' => true,
                        'data' => $responseData
                    ]);
                } catch (RequestException $e) {
                    Log::error('TikTok Status Check Error', [
                        'error' => $e->getMessage(),
                        'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Error checking TikTok status: ' . $e->getMessage()
                    ], 500);
                }
            }
        } catch (\Exception $e) {
            Log::error("TikTok status check error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check publish status: ' . $e->getMessage()
            ], 500);
        }
    }
}
