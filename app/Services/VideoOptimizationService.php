<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\TimeCode;
use Exception;

class VideoOptimizationService
{
    /**
     * Optimize video for web playback
     */
    public function optimizeVideo($file, $fileName)
    {
        try {
            // Store original file
            $originalPath = $file->storeAs('videos/original', $fileName, 'public');
            
            // Get the full path to the stored file
            $storagePath = Storage::disk('public')->path('');
            $originalFullPath = $storagePath . $originalPath;
            
            // Check if FFmpeg is available
            if (!class_exists('FFMpeg\FFMpeg')) {
                Log::warning('FFmpeg not available, storing video as-is');
                return $originalPath;
            }
            
            // Initialize FFmpeg
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => env('FFMPEG_BINARY', '/usr/bin/ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARY', '/usr/bin/ffprobe'),
            ]);
            
            // Open the video
            $video = $ffmpeg->open($originalFullPath);
            
            // Get video dimensions
            $videoInfo = $video->getVideoStream()->getDimensions();
            $width = $videoInfo->getWidth();
            $height = $videoInfo->getHeight();
            
            // Calculate new dimensions (max 720p for web optimization)
            $maxWidth = 720;
            $maxHeight = 1280;
            
            if ($width > $maxWidth || $height > $maxHeight) {
                // Calculate aspect ratio
                $aspectRatio = $width / $height;
                
                if ($width > $height) {
                    // Landscape video
                    $newWidth = min($width, $maxWidth);
                    $newHeight = $newWidth / $aspectRatio;
                } else {
                    // Portrait video
                    $newHeight = min($height, $maxHeight);
                    $newWidth = $newHeight * $aspectRatio;
                }
                
                // Ensure dimensions are even numbers (required by some codecs)
                $newWidth = intval($newWidth);
                $newHeight = intval($newHeight);
                if ($newWidth % 2 != 0) $newWidth++;
                if ($newHeight % 2 != 0) $newHeight++;
                
                // Create format with compression settings
                $format = new X264('libmp3lame');
                $format->setKiloBitrate(1500); // 1.5 Mbps
                $format->setAudioKiloBitrate(128); // 128 Kbps
                
                // Generate optimized filename
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $optimizedFileName = $nameWithoutExtension . '_optimized.mp4';
                $optimizedPath = 'videos/' . $optimizedFileName;
                $optimizedFullPath = $storagePath . $optimizedPath;
                
                // Resize and compress video
                $video->filters()->resize(new Dimension($newWidth, $newHeight))->synchronize();
                $video->save($format, $optimizedFullPath);
                
                // Clean up original file to save space
                // Uncomment the next line if you want to delete the original
                // Storage::disk('public')->delete($originalPath);
                
                return $optimizedPath;
            }
            
            // Video is already small enough, just convert to MP4 if needed
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (strtolower($extension) !== 'mp4') {
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $mp4FileName = $nameWithoutExtension . '.mp4';
                $mp4Path = 'videos/' . $mp4FileName;
                $mp4FullPath = $storagePath . $mp4Path;
                
                // Convert to MP4
                $format = new X264('libmp3lame');
                $format->setKiloBitrate(2000); // 2 Mbps
                $format->setAudioKiloBitrate(128); // 128 Kbps
                
                $video->save($format, $mp4FullPath);
                
                return $mp4Path;
            }
            
            // Return original path if no optimization needed
            return $originalPath;
            
        } catch (Exception $e) {
            Log::error('Error optimizing video: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // If optimization fails, return original path
            return $file->storeAs('videos', $fileName, 'public');
        }
    }
    
    /**
     * Generate thumbnail from video
     */
    public function generateThumbnail($videoPath, $fileName)
    {
        try {
            // Check if FFmpeg is available
            if (!class_exists('FFMpeg\FFMpeg')) {
                Log::warning('FFmpeg not available, cannot generate thumbnail');
                return null;
            }
            
            // Get the full path to the video file
            $storagePath = Storage::disk('public')->path('');
            $videoFullPath = $storagePath . $videoPath;
            
            // Initialize FFmpeg
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => env('FFMPEG_BINARY', '/usr/bin/ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARY', '/usr/bin/ffprobe'),
            ]);
            
            // Open the video
            $video = $ffmpeg->open($videoFullPath);
            
            // Extract filename without extension
            $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
            $thumbnailName = $nameWithoutExtension . '_thumb.jpg';
            $thumbnailPath = 'videos/thumbnails/' . $thumbnailName;
            $thumbnailFullPath = $storagePath . $thumbnailPath;
            
            // Create thumbnails directory if it doesn't exist
            $thumbnailDir = dirname($thumbnailFullPath);
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            
            // Generate thumbnail at 1 second mark
            $video->frame(TimeCode::fromSeconds(1))
                  ->save($thumbnailFullPath);
            
            return $thumbnailPath;
            
        } catch (Exception $e) {
            Log::error('Error generating thumbnail: ' . $e->getMessage(), [
                'exception' => $e,
                'video_path' => $videoPath
            ]);
            
            return null;
        }
    }
    
    /**
     * Get video information
     */
    public function getVideoInfo($videoPath)
    {
        try {
            // Check if FFmpeg is available
            if (!class_exists('FFMpeg\FFMpeg')) {
                return null;
            }
            
            // Get the full path to the video file
            $storagePath = Storage::disk('public')->path('');
            $videoFullPath = $storagePath . $videoPath;
            
            // Initialize FFprobe
            $ffprobe = \FFMpeg\FFProbe::create([
                'ffmpeg.binaries' => env('FFMPEG_BINARY', '/usr/bin/ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARY', '/usr/bin/ffprobe'),
            ]);
            
            // Get video stream information
            $videoStream = $ffprobe->streams($videoFullPath)->videos()->first();
            
            return [
                'duration' => $videoStream->get('duration'),
                'width' => $videoStream->getDimensions()->getWidth(),
                'height' => $videoStream->getDimensions()->getHeight(),
                'codec' => $videoStream->get('codec_name'),
                'bit_rate' => $videoStream->get('bit_rate'),
            ];
            
        } catch (Exception $e) {
            Log::error('Error getting video info: ' . $e->getMessage());
            return null;
        }
    }
}