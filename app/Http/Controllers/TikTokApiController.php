<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class TikTokApiController extends Controller
{
    private $apiBaseUrl = 'https://open.tiktokapis.com/v2/';
    private $accessToken = null;

    public function __construct()
    {
        // Load TikTok API credentials from env or config
        $this->accessToken = env('TIKTOK_ACCESS_TOKEN', null);
    }

    /**
     * Publish content to TikTok
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishContent(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:150',
                'description' => 'required|string|max:2200',
                'media_url' => 'required|url',
                'media_type' => 'required|string|in:PHOTO,VIDEO',
            ]);

            // If no access token is available
            if (!$this->accessToken) {
                throw new Exception('TikTok API credentials are not configured');
            }

            // Prepare the API request payload
            $payload = [
                'post_info' => [
                    'title' => $request->title,
                    'description' => $request->description,
                    'disable_comment' => false,
                    'privacy_level' => 'PUBLIC_TO_EVERYONE',
                    'auto_add_music' => true
                ],
                'source_info' => [
                    'source' => 'PULL_FROM_URL',
                ],
                'post_mode' => 'DIRECT_POST',
                'media_type' => $request->media_type
            ];

            // Add the appropriate media source based on type
            if ($request->media_type === 'PHOTO') {
                $payload['source_info']['photo_cover_index'] = 0;
                $payload['source_info']['photo_images'] = [$request->media_url];
            } else {
                $payload['source_info']['video_url'] = $request->media_url;
            }

            // Make the actual API call to TikTok
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiBaseUrl . 'post/publish/content/init/', $payload);

            // Check if the request was successful
            if ($response->successful()) {
                $data = $response->json();
                
                // TikTok API returns a publish_id we need to track
                if (isset($data['data']['publish_id'])) {
                    // You would typically store this publish_id and check its status later
                    return response()->json([
                        'success' => true,
                        'message' => 'Content published to TikTok successfully',
                        'publish_id' => $data['data']['publish_id'],
                        'data' => $data
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Request to TikTok API was successful',
                    'data' => $data
                ]);
            }
            
            // Handle error response
            Log::error('TikTok API error: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish content to TikTok',
                'error' => $response->json() ?: $response->body()
            ], 500);
            
        } catch (Exception $e) {
            Log::error('TikTok API exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while publishing to TikTok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check the status of a published content
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPublishStatus(Request $request)
    {
        try {
            $request->validate([
                'publish_id' => 'required|string'
            ]);

            if (!$this->accessToken) {
                throw new Exception('TikTok API credentials are not configured');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->get($this->apiBaseUrl . 'post/publish/status/query/', [
                'publish_id' => $request->publish_id
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            Log::error('TikTok API status check error: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check publish status',
                'error' => $response->json() ?: $response->body()
            ], 500);

        } catch (Exception $e) {
            Log::error('TikTok API status check exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking publish status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
