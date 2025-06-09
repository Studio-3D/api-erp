<?php

namespace App\Http\Controllers\LinkedIn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;

class LinkedInController extends Controller
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    
    public function __construct()
    {
        $this->clientId = env('LINKEDIN_CLIENT_ID', '');
        $this->clientSecret = env('LINKEDIN_CLIENT_SECRET', '');
        $this->redirectUri = env('LINKEDIN_REDIRECT_URI', '');
    }
    
    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string',
                'state' => 'required|string'
            ]);
            
            $code = $request->input('code');
            
            $client = new Client();
            
            // Exchange code for access token
            $tokenResponse = $client->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]
            ]);
            
            $tokenData = json_decode($tokenResponse->getBody()->getContents(), true);
            
            // Use the OpenID Connect userinfo endpoint instead of /v2/me
            $profileResponse = $client->get('https://api.linkedin.com/v2/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenData['access_token'],
                ]
            ]);
            
            $profile = json_decode($profileResponse->getBody()->getContents(), true);
            
            // Log the successful authentication
            Log::info('LinkedIn authentication successful', [
                'user_id' => $profile['sub'] ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'access_token' => $tokenData['access_token'],
                'expires_in' => $tokenData['expires_in'],
                'profile' => $profile
            ]);
            
        } catch (RequestException $e) {
            Log::error('LinkedIn OAuth error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate with LinkedIn: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Share content on LinkedIn
     */
    public function sharePost(Request $request)
    {
        try {
            $request->validate([
                'accessToken' => 'required|string',
                'content' => 'required|string|max:3000',
                'visibility' => 'required|string|in:PUBLIC,CONNECTIONS'
            ]);
            
            $accessToken = $request->input('accessToken');
            $content = $request->input('content');
            $visibility = $request->input('visibility');
            $mediaUrl = $request->input('mediaUrl');
            
            $client = new Client();
            
            // Get user identifier from userinfo endpoint
            $profileResponse = $client->get('https://api.linkedin.com/v2/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken
                ]
            ]);
            
            $profile = json_decode($profileResponse->getBody()->getContents(), true);
            $personUrn = $profile['sub']; // Use 'sub' from OpenID instead of 'id'
            
            // Person URN format needs to be adjusted
            $personUrn = str_replace('linkedin:', '', $personUrn);
            
            // Prepare the share payload
            $sharePayload = [
                'author' => 'urn:li:person:' . $personUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $content
                        ],
                        'shareMediaCategory' => 'NONE'
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => $visibility
                ]
            ];
            
            // If media URL is provided, register it with LinkedIn first
            if ($mediaUrl) {
                // Step 1: Register upload with LinkedIn to get an upload URL
                $mediaType = $this->getMediaTypeFromUrl($mediaUrl);
                
                if ($mediaType === 'IMAGE') {
                    $registerUploadResponse = $client->post('https://api.linkedin.com/v2/assets?action=registerUpload', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json'
                        ],
                        'json' => [
                            'registerUploadRequest' => [
                                'recipes' => [
                                    'urn:li:digitalmediaRecipe:feedshare-image'
                                ],
                                'owner' => 'urn:li:person:' . $personUrn,
                                'serviceRelationships' => [
                                    [
                                        'relationshipType' => 'OWNER',
                                        'identifier' => 'urn:li:userGeneratedContent'
                                    ]
                                ]
                            ]
                        ]
                    ]);
                    
                    $registerUploadData = json_decode($registerUploadResponse->getBody()->getContents(), true);
                    
                    // Step 2: Get the upload URL and asset URN
                    $uploadUrl = $registerUploadData['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
                    $assetUrn = $registerUploadData['value']['asset'];
                    
                    // Step 3: Fetch the media content using a more reliable method
                    try {
                        // First, try to use Guzzle to download the content
                        $mediaResponse = $client->get($mediaUrl, [
                            'timeout' => 30, // Increase timeout for large files
                        ]);
                        $mediaContent = $mediaResponse->getBody()->getContents();
                    } catch (\Exception $e) {
                        // If the URL is from our own server, try using an absolute URL
                        if (strpos($mediaUrl, 'localhost') !== false || strpos($mediaUrl, '127.0.0.1') !== false) {
                            // Try using your hosted domain instead of localhost
                            $hostedUrl = str_replace(['localhost', '127.0.0.1'], env('APP_URL_HOST', 'your-hosted-domain.com'), $mediaUrl);
                            
                            $mediaResponse = $client->get($hostedUrl, [
                                'timeout' => 30,
                            ]);
                            $mediaContent = $mediaResponse->getBody()->getContents();
                        } else {
                            // If all else fails, try using cURL directly
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $mediaUrl);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            $mediaContent = curl_exec($ch);
                            curl_close($ch);
                            
                            if (!$mediaContent) {
                                throw new \Exception("Could not download media from URL using any method: {$mediaUrl}");
                            }
                        }
                    }
                    
                    // Step 4: Upload the media to LinkedIn
                    $client->put($uploadUrl, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken
                        ],
                        'body' => $mediaContent
                    ]);
                    
                    // Step 5: Include the media URN in the share request
                    $sharePayload['specificContent']['com.linkedin.ugc.ShareContent']['shareMediaCategory'] = 'IMAGE';
                    $sharePayload['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
                        [
                            'status' => 'READY',
                            'description' => [
                                'text' => 'Property Image'
                            ],
                            'media' => $assetUrn,
                            'title' => [
                                'text' => 'Property Image'
                            ]
                        ]
                    ];
                } else {
                    // If it's not an image, default to text-only post
                    Log::warning('LinkedIn only supports image sharing. Defaulting to text-only post.');
                }
            }
            
            // Create the share
            $shareResponse = $client->post('https://api.linkedin.com/v2/ugcPosts', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'X-Restli-Protocol-Version' => '2.0.0'
                ],
                'json' => $sharePayload
            ]);
            
            $shareData = json_decode($shareResponse->getBody()->getContents(), true);
            
            Log::info('LinkedIn post created', [
                'post_id' => $shareData['id'] ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'post' => $shareData
            ]);
            
        } catch (RequestException $e) {
            Log::error('LinkedIn share error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to share on LinkedIn: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Determine media type from URL
     */
    private function getMediaTypeFromUrl($url) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $pathInfo = pathinfo($url);
        
        if (isset($pathInfo['extension'])) {
            $extension = strtolower($pathInfo['extension']);
            if (in_array($extension, $imageExtensions)) {
                return 'IMAGE';
            }
        }
        
        // Try to determine by making a HEAD request
        try {
            $client = new Client();
            $response = $client->head($url);
            $contentType = $response->getHeaderLine('Content-Type');
            
            if (strpos($contentType, 'image/') === 0) {
                return 'IMAGE';
            }
        } catch (\Exception $e) {
            // Ignore exceptions from HEAD request
        }
        
        // Default to assuming it's an image if we can't determine
        return 'IMAGE';
    }
}
