<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\SriLankaVideo;
use App\Services\VideoOptimizationService;
use Exception;

class SteamLankaController extends Controller
{
    private $videoOptimizationService;
    
    // API configuration
    private $youtubeApiKey;
    private $instagramAccessToken;
    private $tiktokApiKey;
    
    // Social media platforms we support
    private $supportedPlatforms = ['youtube', 'instagram', 'tiktok'];
    
    // Sri Lankan tourist keywords for trending searches
    private $sriLankanKeywords = [
        'sri lanka travel', 'sri lanka tourism', 'sri lanka places to visit',
        'sigiriya', 'kandy', 'galle fort', 'ella', 'anuradhapura', 'polonnaruwa',
        'yala national park', 'mirissa', 'unawatuna', 'bentota', 'negombo',
        'nuwara eliya', 'adam\'s peak', 'temple of tooth', 'dambulla cave',
        'sri lanka beaches', 'sri lanka mountains', 'sri lanka culture',
        'sri lanka food', 'sri lanka train journey', 'sri lanka wildlife'
    ];
    
    public function __construct(VideoOptimizationService $videoOptimizationService)
    {
        $this->videoOptimizationService = $videoOptimizationService;
        
        // Get API keys from environment variables
        $this->youtubeApiKey = env('YOUTUBE_API_KEY');
        $this->instagramAccessToken = env('INSTAGRAM_ACCESS_TOKEN');
        $this->tiktokApiKey = env('TIKTOK_API_KEY');
    }
    
    /**
     * Display the SteamLanka page
     */
    public function index()
    {
        // In a real implementation, you would fetch videos here
        // $videos = $this->fetchSocialMediaVideos();
        
        // For now, we'll pass an empty array and let the frontend handle sample data
        $videos = [];
        
        return view('steamlanka', compact('videos'));
    }
    
    /**
     * Fetch Sri Lankan travel videos from database
     * Real data only - no sample data
     */
    public function fetchSriLankaVideos(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            // Fetch real videos from database
            $videos = SriLankaVideo::with('user')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'description' => $video->description,
                        'video_url' => $video->video_url,
                        'thumbnail' => $video->thumbnail,
                        'location' => $video->location,
                        'hashtags' => $video->hashtags ?? [],
                        'likes_count' => $video->likes_count,
                        'comments_count' => $video->comments_count,
                        'shares_count' => $video->shares_count,
                        'username' => $video->user ? $video->user->name : 'Anonymous',
                        'user_avatar' => $video->user ? substr($video->user->name, 0, 1) : 'A',
                        'created_at' => $video->created_at->format('Y-m-d H:i:s')
                    ];
                });
            
            $totalVideos = SriLankaVideo::where('is_active', true)->count();
            $hasMore = ($offset + $limit) < $totalVideos;
            
            return response()->json([
                'success' => true,
                'videos' => $videos,
                'has_more' => $hasMore,
                'current_page' => $page,
                'total_count' => $totalVideos
            ]);
            
        } catch (Exception $e) {
            Log::error('Error fetching Sri Lankan videos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading videos',
                'videos' => [],
                'has_more' => false
            ], 500);
        }
    }
    
    /**
     * Upload a new Sri Lankan travel video
     */
    public function uploadVideo(Request $request)
    {
        try {
            Log::info('Video upload request received', [
                'has_file' => $request->hasFile('video'),
                'all_request_data' => $request->all()
            ]);
            
            // Relaxed validation to support various social media URL formats (including TikTok)
            $validator = \Validator::make($request->all(), [
                'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,quicktime,webm|max:51200', 
                'social_link' => 'nullable|string|max:1000', 
                'title' => 'required|string|max:255',
                'location' => 'required|string|max:255'
            ]);
            
            if ($validator->fails()) {
                Log::error('Video upload validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Get validated data
            $validated = $validator->validated();
            
            // Handle file upload
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.mp4';
                $optimizedFilePath = $this->videoOptimizationService->optimizeVideo($file, $fileName);
                $thumbnailPath = $this->videoOptimizationService->generateThumbnail($optimizedFilePath, $fileName);
                $videoUrl = Storage::url($optimizedFilePath);
            } else {
                // Use social link as the video URL
                $rawLink = $request->input('social_link');
                
                // Smart Resolution for Shortened Links (TikTok, etc.)
                $videoUrl = $rawLink;
                try {
                    if (strpos($rawLink, 'vm.tiktok.com') !== false || strpos($rawLink, 'vt.tiktok.com') !== false) {
                        $response = Http::withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                        ])->get($rawLink);
                        
                        if ($response->effectiveUri()) {
                            $videoUrl = (string) $response->effectiveUri();
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('URL Resolution failed: ' . $e->getMessage());
                }
                
                $thumbnailPath = null;
            }

            // Parse hashtags
            $hashtags = [];
            if (!empty($validated['hashtags'])) {
                $hashtags = array_filter(array_map('trim', explode(' ', $validated['hashtags'])));
            }
            
            // Create video record in database
            $video = SriLankaVideo::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'video_url' => $videoUrl,
                'thumbnail' => $thumbnailPath ? Storage::url($thumbnailPath) : null,
                'location' => $validated['location'],
                'hashtags' => $hashtags,
                'likes_count' => 0,
                'comments_count' => 0,
                'shares_count' => 0,
                'views_count' => 0,
                'is_active' => true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Adventure shared successfully',
                'video_id' => $video->id
            ]);
            
            Log::warning('No video file provided in request');
            
            return response()->json([
                'success' => false,
                'message' => 'No video file provided'
            ], 400);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Video upload validation exception', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error uploading video: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error uploading video: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Fetch trending videos from all platforms
     */
    private function fetchTrendingVideos($query = '', $platform = 'all', $limit = 12)
    {
        $allVideos = [];
        
        // Fetch from YouTube if API key is available
        if ($this->youtubeApiKey && ($platform === 'all' || $platform === 'youtube')) {
            $youtubeVideos = $this->fetchYouTubeTrendingVideos($query, ceil($limit / 3));
            $allVideos = array_merge($allVideos, $youtubeVideos);
        }
        
        // Fetch from Instagram if access token is available
        if ($this->instagramAccessToken && ($platform === 'all' || $platform === 'instagram')) {
            $instagramVideos = $this->fetchInstagramTrendingVideos($query, ceil($limit / 3));
            $allVideos = array_merge($allVideos, $instagramVideos);
        }
        
        // Fetch from TikTok if API key is available
        if ($this->tiktokApiKey && ($platform === 'all' || $platform === 'tiktok')) {
            $tiktokVideos = $this->fetchTikTokTrendingVideos($query, ceil($limit / 3));
            $allVideos = array_merge($allVideos, $tiktokVideos);
        }
        
        // If no API integrations available, use enhanced sample data
        if (empty($allVideos)) {
            $allVideos = $this->getEnhancedSampleVideos();
        }
        
        // Sort by trending score (views, engagement, recency)
        usort($allVideos, function($a, $b) {
            return $b['trending_score'] <=> $a['trending_score'];
        });
        
        // Filter by query if provided
        if (!empty($query)) {
            $allVideos = array_filter($allVideos, function($video) use ($query) {
                $searchTerms = strtolower($query);
                return stripos($video['title'], $searchTerms) !== false || 
                       stripos($video['description'], $searchTerms) !== false ||
                       stripos($video['location'], $searchTerms) !== false ||
                       stripos($video['channel'], $searchTerms) !== false ||
                       stripos($video['tags'], $searchTerms) !== false;
            });
            $allVideos = array_values($allVideos);
        }
        
        return array_slice($allVideos, 0, $limit);
    }
    
    /**
     * Get sample video data with real YouTube video IDs
     */
    private function getSampleVideos()
    {
        return [
            [
                'id' => 'dQw4w9WgXcQ',
                'title' => 'Sigiriya Rock Fortress Tour',
                'description' => 'Ancient rock fortress and palace ruin situated in the central Matale District of Sri Lanka.',
                'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                'channel' => 'Sri Lanka Travel Guide',
                'views' => '125K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'location' => 'Central Province',
                'duration' => '5:30'
            ],
            [
                'id' => 'jNQXAC9IVRw',
                'title' => 'Kandy Temple of the Sacred Tooth',
                'description' => 'Home to the Temple of the Sacred Tooth Relic, one of the most sacred places of worship in Sri Lanka.',
                'thumbnail' => 'https://i.ytimg.com/vi/jNQXAC9IVRw/hqdefault.jpg',
                'channel' => 'Cultural Sri Lanka',
                'views' => '98K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'location' => 'Central Province',
                'duration' => '3:45'
            ],
            [
                'id' => 'M7lc1UVf-VE',
                'title' => 'Galle Fort Walking Tour',
                'description' => 'A historical fortification in the Bay of Galle on the southwest coast of Sri Lanka.',
                'thumbnail' => 'https://i.ytimg.com/vi/M7lc1UVf-VE/hqdefault.jpg',
                'channel' => 'Heritage Sri Lanka',
                'views' => '87K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
                'location' => 'Southern Province',
                'duration' => '4:15'
            ],
            [
                'id' => '9bZkp7q19f0',
                'title' => 'Ella Rock Hiking Adventure',
                'description' => 'Breathtaking views of the Ella Gap and surrounding mountains from the top of Ella Rock.',
                'thumbnail' => 'https://i.ytimg.com/vi/9bZkp7q19f0/hqdefault.jpg',
                'channel' => 'Adventure Lanka',
                'views' => '156K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'location' => 'Uva Province',
                'duration' => '8:22'
            ],
            [
                'id' => 'kJQP7kiw5Fk',
                'title' => 'Whale Watching in Mirissa',
                'description' => 'Experience whale watching in one of the best locations in the world for blue whale sightings.',
                'thumbnail' => 'https://i.ytimg.com/vi/kJQP7kiw5Fk/hqdefault.jpg',
                'channel' => 'Ocean Adventures SL',
                'views' => '210K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                'location' => 'Southern Province',
                'duration' => '6:40'
            ],
            [
                'id' => 'YQHsXMglC9A',
                'title' => 'Anuradhapura Ancient Ruins',
                'description' => 'One of the ancient capitals of Sri Lanka, famous for its well-preserved ruins of ancient Sri Lankan civilization.',
                'thumbnail' => 'https://i.ytimg.com/vi/YQHsXMglC9A/hqdefault.jpg',
                'channel' => 'History of Sri Lanka',
                'views' => '76K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=YQHsXMglC9A',
                'location' => 'North Central Province',
                'duration' => '7:18'
            ],
            [
                'id' => 'dQw4w9WgXcQ',
                'title' => 'Train Ride to Ella - Best Views',
                'description' => 'Scenic train journey through the mountains of Sri Lanka to Ella with breathtaking views.',
                'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                'channel' => 'Sri Lanka Trains',
                'views' => '320K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'location' => 'Uva Province',
                'duration' => '12:05'
            ],
            [
                'id' => 'jNQXAC9IVRw',
                'title' => 'Beaches of Unawatuna',
                'description' => 'Beautiful crescent-shaped bay with calm waters, coral reefs and golden sand.',
                'thumbnail' => 'https://i.ytimg.com/vi/jNQXAC9IVRw/hqdefault.jpg',
                'channel' => 'Beach Lovers SL',
                'views' => '189K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'location' => 'Southern Province',
                'duration' => '2:30'
            ]
        ];
    }
    
    /**
     * Fetch trending videos from YouTube using the YouTube Data API v3
     */
    private function fetchYouTubeTrendingVideos($query = '', $limit = 8)
    {
        try {
            if (!$this->youtubeApiKey) {
                return [];
            }
            
            $videos = [];
            $searchTerms = !empty($query) ? $query : implode('|', array_slice($this->sriLankanKeywords, 0, 5));
            
            // Search for trending videos with Sri Lankan keywords
            $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'q' => $searchTerms,
                'type' => 'video',
                'order' => 'relevance', // Use 'relevance' for trending content
                'regionCode' => 'LK', // Sri Lanka region
                'maxResults' => $limit * 2, // Get more results to filter
                'publishedAfter' => now()->subDays(30)->toISOString(), // Last 30 days
                'key' => $this->youtubeApiKey
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                foreach ($data['items'] ?? [] as $item) {
                    $videoId = $item['id']['videoId'];
                    $snippet = $item['snippet'];
                    
                    // Get additional video details for view count and duration
                    $videoDetails = $this->getYouTubeVideoDetails($videoId);
                    
                    if ($videoDetails) {
                        $videos[] = [
                            'id' => $videoId,
                            'title' => $snippet['title'],
                            'description' => $this->truncateDescription($snippet['description']),
                            'thumbnail' => $snippet['thumbnails']['high']['url'] ?? $snippet['thumbnails']['default']['url'],
                            'channel' => $snippet['channelTitle'],
                            'views' => $this->formatViewCount($videoDetails['viewCount']),
                            'platform' => 'youtube',
                            'url' => "https://www.youtube.com/watch?v=" . $videoId,
                            'location' => $this->extractLocationFromTitle($snippet['title']),
                            'duration' => $videoDetails['duration'],
                            'published_at' => $snippet['publishedAt'],
                            'trending_score' => $this->calculateTrendingScore($videoDetails['viewCount'], $snippet['publishedAt']),
                            'tags' => implode(', ', $videoDetails['tags'] ?? [])
                        ];
                    }
                }
            }
            
            return array_slice($videos, 0, $limit);
            
        } catch (Exception $e) {
            Log::error('Error fetching YouTube trending videos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get detailed information about a YouTube video
     */
    private function getYouTubeVideoDetails($videoId)
    {
        try {
            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'statistics,contentDetails,snippet',
                'id' => $videoId,
                'key' => $this->youtubeApiKey
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $item = $data['items'][0] ?? null;
                
                if ($item) {
                    return [
                        'viewCount' => (int) ($item['statistics']['viewCount'] ?? 0),
                        'duration' => $this->formatDuration($item['contentDetails']['duration'] ?? 'PT0S'),
                        'tags' => $item['snippet']['tags'] ?? []
                    ];
                }
            }
            
            return null;
        } catch (Exception $e) {
            Log::error('Error fetching YouTube video details: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Fetch videos from Instagram using Instagram Basic Display API
     */
    private function fetchInstagramTrendingVideos($query = '', $limit = 8)
    {
        try {
            if (!$this->instagramAccessToken) {
                return [];
            }
            
            // Instagram API integration would go here
            // For now, return sample Instagram videos with trending characteristics
            return $this->getInstagramSampleVideos($limit);
            
        } catch (Exception $e) {
            Log::error('Error fetching Instagram trending videos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Fetch videos from TikTok using TikTok API
     */
    private function fetchTikTokTrendingVideos($query = '', $limit = 8)
    {
        try {
            if (!$this->tiktokApiKey) {
                return [];
            }
            
            // TikTok API integration would go here
            // For now, return sample TikTok videos with trending characteristics
            return $this->getTikTokSampleVideos($limit);
            
        } catch (Exception $e) {
            Log::error('Error fetching TikTok trending videos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Fetch videos from a specific YouTube channel
     * This method scrapes public YouTube channel data
     */
    private function fetchYouTubeVideos($channelId = 'srilankatravelvideo', $limit = 8)
    {
        try {
            // For now, we'll return sample videos from the Sri Lanka Travel Video channel
            // In a production environment, you would implement actual YouTube API calls
            
            $sampleVideos = [
                [
                    'id' => '1',
                    'title' => 'Best of Sri Lanka - 4K Travel Guide',
                    'description' => 'Experience the beauty of Sri Lanka in this comprehensive travel guide featuring stunning locations across the island.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '2.1M',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'Sri Lanka',
                    'duration' => '12:45'
                ],
                [
                    'id' => '2',
                    'title' => 'Sigiriya Rock Fortress - Ancient Wonder of Sri Lanka',
                    'description' => 'Explore the ancient rock fortress of Sigiriya, a UNESCO World Heritage Site and one of the most impressive architectural feats of ancient Sri Lanka.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '1.5M',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'Central Province',
                    'duration' => '8:32'
                ],
                [
                    'id' => '3',
                    'title' => 'Galle Fort - Colonial Heritage of Sri Lanka',
                    'description' => 'Discover the historic Galle Fort, a well-preserved fortress built by the Portuguese and later expanded by the Dutch.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '980K',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'Southern Province',
                    'duration' => '10:15'
                ],
                [
                    'id' => '4',
                    'title' => 'Train Journey to Ella - Most Beautiful Railway in the World',
                    'description' => 'Experience one of the most scenic train rides in the world from Kandy to Ella through the lush hills of Sri Lanka.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '3.2M',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'Uva Province',
                    'duration' => '15:22'
                ],
                [
                    'id' => '5',
                    'title' => 'Yala National Park Safari - Wildlife Adventure',
                    'description' => 'Embark on an exciting safari in Yala National Park, home to leopards, elephants, and diverse wildlife.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '1.8M',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'Southern Province',
                    'duration' => '18:40'
                ],
                [
                    'id' => '6',
                    'title' => 'Temple of the Sacred Tooth Relic - Kandy',
                    'description' => 'Visit the sacred Temple of the Tooth in Kandy, one of the most important pilgrimage sites for Buddhists.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '1.2M',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'Central Province',
                    'duration' => '9:18'
                ],
                [
                    'id' => '7',
                    'title' => 'Mirissa Beach - Whale Watching Paradise',
                    'description' => 'Experience whale watching in Mirissa, one of the best places in the world to see blue whales and sperm whales.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '2.5M',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'Southern Province',
                    'duration' => '14:35'
                ],
                [
                    'id' => '8',
                    'title' => 'Anuradhapura - Ancient Sacred City',
                    'description' => 'Explore the ancient city of Anuradhapura, a UNESCO World Heritage Site with over 2500 years of history.',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'channel' => 'Sri Lanka Travel Video',
                    'views' => '890K',
                    'platform' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'location' => 'North Central Province',
                    'duration' => '11:50'
                ]
            ];
            
            return array_slice($sampleVideos, 0, $limit);
        } catch (Exception $e) {
            // Log error and return empty array
            \Log::error('Error fetching YouTube videos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get enhanced sample videos with real YouTube video IDs
     */
    private function getEnhancedSampleVideos()
    {
        return [
            [
                'id' => 'dQw4w9WgXcQ',
                'title' => 'Sigiriya Rock Fortress - Ancient Wonder of Sri Lanka',
                'description' => 'Explore the ancient rock fortress of Sigiriya, a UNESCO World Heritage Site and one of the most impressive architectural feats of ancient Sri Lanka.',
                'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                'channel' => 'Sri Lanka Travel Video',
                'views' => '2.1M',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'location' => 'Central Province',
                'duration' => '12:45',
                'trending_score' => 95,
                'tags' => 'sigiriya, sri lanka, travel, ancient, fortress, unesco',
                'published_at' => now()->subDays(2)->toISOString()
            ],
            [
                'id' => 'jNQXAC9IVRw',
                'title' => 'Train Journey to Ella - Most Beautiful Railway in the World',
                'description' => 'Experience one of the most scenic train rides in the world from Kandy to Ella through the lush hills of Sri Lanka.',
                'thumbnail' => 'https://i.ytimg.com/vi/jNQXAC9IVRw/hqdefault.jpg',
                'channel' => 'Sri Lanka Adventures',
                'views' => '3.2M',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'location' => 'Uva Province',
                'duration' => '15:22',
                'trending_score' => 92,
                'tags' => 'ella, train, sri lanka, scenic, journey, mountains',
                'published_at' => now()->subDays(1)->toISOString()
            ],
            [
                'id' => 'M7lc1UVf-VE',
                'title' => 'Mirissa Beach - Whale Watching Paradise',
                'description' => 'Experience whale watching in Mirissa, one of the best places in the world to see blue whales and sperm whales.',
                'thumbnail' => 'https://i.ytimg.com/vi/M7lc1UVf-VE/hqdefault.jpg',
                'channel' => 'Ocean Adventures SL',
                'views' => '2.5M',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
                'location' => 'Southern Province',
                'duration' => '14:35',
                'trending_score' => 88,
                'tags' => 'mirissa, whale watching, sri lanka, ocean, wildlife',
                'published_at' => now()->subHours(6)->toISOString()
            ],
            [
                'id' => '9bZkp7q19f0',
                'title' => 'Yala National Park Safari - Wildlife Adventure',
                'description' => 'Embark on an exciting safari in Yala National Park, home to leopards, elephants, and diverse wildlife.',
                'thumbnail' => 'https://i.ytimg.com/vi/9bZkp7q19f0/hqdefault.jpg',
                'channel' => 'Wildlife Sri Lanka',
                'views' => '1.8M',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'location' => 'Southern Province',
                'duration' => '18:40',
                'trending_score' => 85,
                'tags' => 'yala, safari, wildlife, leopard, elephant, sri lanka',
                'published_at' => now()->subHours(12)->toISOString()
            ],
            [
                'id' => 'kJQP7kiw5Fk',
                'title' => 'Galle Fort - Colonial Heritage of Sri Lanka',
                'description' => 'Discover the historic Galle Fort, a well-preserved fortress built by the Portuguese and later expanded by the Dutch.',
                'thumbnail' => 'https://i.ytimg.com/vi/kJQP7kiw5Fk/hqdefault.jpg',
                'channel' => 'Heritage Sri Lanka',
                'views' => '980K',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                'location' => 'Southern Province',
                'duration' => '10:15',
                'trending_score' => 82,
                'tags' => 'galle fort, colonial, heritage, dutch, portuguese, sri lanka',
                'published_at' => now()->subDays(3)->toISOString()
            ],
            [
                'id' => 'YQHsXMglC9A',
                'title' => 'Temple of the Sacred Tooth Relic - Kandy',
                'description' => 'Visit the sacred Temple of the Tooth in Kandy, one of the most important pilgrimage sites for Buddhists.',
                'thumbnail' => 'https://i.ytimg.com/vi/YQHsXMglC9A/hqdefault.jpg',
                'channel' => 'Cultural Sri Lanka',
                'views' => '1.2M',
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=YQHsXMglC9A',
                'location' => 'Central Province',
                'duration' => '9:18',
                'trending_score' => 80,
                'tags' => 'kandy, temple, sacred tooth, buddhist, pilgrimage, sri lanka',
                'published_at' => now()->subDays(4)->toISOString()
            ]
        ];
    }
    
    /**
     * Get sample Instagram videos with trending characteristics
     */
    private function getInstagramSampleVideos($limit = 4)
    {
        return [
            [
                'id' => 'ig_1',
                'title' => 'Sunset at Unawatuna Beach',
                'description' => 'Golden hour magic at one of Sri Lanka\'s most beautiful beaches.',
                'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                'channel' => 'Beach Lovers SL',
                'views' => '189K',
                'platform' => 'instagram',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'location' => 'Southern Province',
                'duration' => '2:30',
                'trending_score' => 75,
                'tags' => 'unawatuna, beach, sunset, sri lanka, golden hour',
                'published_at' => now()->subHours(8)->toISOString()
            ],
            [
                'id' => 'ig_2',
                'title' => 'Adam\'s Peak Sunrise Hike',
                'description' => 'Incredible sunrise views from the sacred mountain peak.',
                'thumbnail' => 'https://i.ytimg.com/vi/jNQXAC9IVRw/hqdefault.jpg',
                'channel' => 'Mountain Adventures',
                'views' => '245K',
                'platform' => 'instagram',
                'url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'location' => 'Sabaragamuwa Province',
                'duration' => '3:45',
                'trending_score' => 78,
                'tags' => 'adams peak, sunrise, hike, sacred mountain, sri lanka',
                'published_at' => now()->subHours(4)->toISOString()
            ]
        ];
    }
    
    /**
     * Get sample TikTok videos with trending characteristics
     */
    private function getTikTokSampleVideos($limit = 4)
    {
        return [
            [
                'id' => 'tt_1',
                'title' => 'Sri Lankan Food Adventure',
                'description' => 'Trying the most delicious Sri Lankan street food!',
                'thumbnail' => 'https://i.ytimg.com/vi/M7lc1UVf-VE/hqdefault.jpg',
                'channel' => 'Food Explorer SL',
                'views' => '320K',
                'platform' => 'tiktok',
                'url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
                'location' => 'Colombo',
                'duration' => '1:30',
                'trending_score' => 85,
                'tags' => 'sri lanka food, street food, curry, hoppers, kottu',
                'published_at' => now()->subHours(2)->toISOString()
            ],
            [
                'id' => 'tt_2',
                'title' => 'Dambulla Cave Temple Tour',
                'description' => 'Ancient Buddhist cave temple with incredible frescoes.',
                'thumbnail' => 'https://i.ytimg.com/vi/9bZkp7q19f0/hqdefault.jpg',
                'channel' => 'History Explorer',
                'views' => '156K',
                'platform' => 'tiktok',
                'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'location' => 'Central Province',
                'duration' => '2:15',
                'trending_score' => 72,
                'tags' => 'dambulla, cave temple, buddhist, frescoes, ancient',
                'published_at' => now()->subHours(10)->toISOString()
            ]
        ];
    }
    
    /**
     * Helper methods for video processing
     */
    private function formatViewCount($count)
    {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        } elseif ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }
        return (string) $count;
    }
    
    private function formatDuration($iso8601Duration)
    {
        $interval = new \DateInterval($iso8601Duration);
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%d:%02d', $minutes, $seconds);
    }
    
    private function truncateDescription($description, $length = 150)
    {
        if (strlen($description) <= $length) {
            return $description;
        }
        return substr($description, 0, $length) . '...';
    }
    
    private function extractLocationFromTitle($title)
    {
        $locations = [
            'Sigiriya' => 'Central Province',
            'Kandy' => 'Central Province',
            'Galle' => 'Southern Province',
            'Ella' => 'Uva Province',
            'Anuradhapura' => 'North Central Province',
            'Polonnaruwa' => 'North Central Province',
            'Yala' => 'Southern Province',
            'Mirissa' => 'Southern Province',
            'Unawatuna' => 'Southern Province',
            'Bentota' => 'Western Province',
            'Negombo' => 'Western Province',
            'Nuwara Eliya' => 'Central Province',
            'Adam\'s Peak' => 'Sabaragamuwa Province',
            'Dambulla' => 'Central Province'
        ];
        
        foreach ($locations as $location => $province) {
            if (stripos($title, $location) !== false) {
                return $province;
            }
        }
        
        return 'Sri Lanka';
    }
    
    private function calculateTrendingScore($viewCount, $publishedAt)
    {
        $daysOld = now()->diffInDays($publishedAt);
        $viewScore = min($viewCount / 100000, 100); // Normalize view count
        $recencyScore = max(100 - ($daysOld * 5), 0); // Recent videos score higher
        
        return round(($viewScore * 0.7) + ($recencyScore * 0.3));
    }
}