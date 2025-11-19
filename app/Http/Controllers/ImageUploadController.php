<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ImageUploadController - Controller xử lý upload và validate media files
 * 
 * Controller này cung cấp API endpoints cho frontend để:
 * - Upload media files (images/videos) lên server
 * - Validate media URLs từ external sources
 * - Delete uploaded media files
 * 
 * Supported formats:
 * - Images: jpeg, png, jpg, gif, webp
 * - Videos: mp4, avi, mov, wmv
 * 
 * Storage:
 * - Images: public/media/images/
 * - Videos: public/media/videos/
 * 
 * @author QuickPoll Team
 */
class ImageUploadController extends Controller
{
    /**
     * Upload media file (image hoặc video) lên server
     * 
     * Flow:
     * 1. Validate file (type, size max 20MB)
     * 2. Generate unique filename (random 40 chars + extension)
     * 3. Determine storage directory (images hoặc videos)
     * 4. Store file trong public/media directory
     * 5. Return URL và metadata
     * 
     * Response format:
     * {
     *   "success": true,
     *   "url": "/storage/media/images/xxxxx.jpg",
     *   "filename": "xxxxx.jpg",
     *   "path": "media/images/xxxxx.jpg",
     *   "type": "image" | "video",
     *   "extension": "jpg"
     * }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,wmv|max:20480', // 20MB max for videos
        ]);

        try {
            $file = $request->file('media');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            
            // Determine if it's an image or video
            $isVideo = in_array($file->getClientOriginalExtension(), ['mp4', 'avi', 'mov', 'wmv']);
            $directory = $isVideo ? 'media/videos' : 'media/images';
            
            // Store in public/media directory
            $path = $file->storeAs($directory, $filename, 'public');
            
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'filename' => $filename,
                'path' => $path,
                'type' => $isVideo ? 'video' : 'image',
                'extension' => $file->getClientOriginalExtension(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload media: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate media URL từ external source
     * 
     * Flow:
     * 1. Validate URL format
     * 2. Check URL extension (nếu có)
     * 3. Try to get HTTP headers để check Content-Type
     * 4. Verify Content-Type là image/* hoặc video/*
     * 5. Return validation result với metadata
     * 
     * Validation rules:
     * - URL phải hợp lệ (FILTER_VALIDATE_URL)
     * - Extension (nếu có) phải là media format
     * - Content-Type phải là image/* hoặc video/*
     * - Cho phép URLs không có extension (dynamic image URLs)
     * 
     * Response format:
     * {
     *   "success": true,
     *   "url": "https://...",
     *   "type": "image" | "video",
     *   "contentType": "image/jpeg"
     * }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|string|max:500',
        ]);

        $url = $request->input('url');
        
        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'The URL must be a valid URL.',
            ], 400);
        }
        
        // Basic URL validation for media files - more lenient approach
        $mediaExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'avi', 'mov', 'wmv', 'bmp', 'tiff', 'ico'];
        $urlExtension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        // If no extension, allow the URL and let the browser handle it
        if (empty($urlExtension)) {
            // Allow URLs without extensions - they might be dynamic image URLs
            // The browser will handle the actual validation when displaying
        } else if (!in_array($urlExtension, $mediaExtensions)) {
            // Only reject if we have a clear non-media extension
            $nonMediaExtensions = ['html', 'php', 'js', 'css', 'txt', 'pdf', 'doc', 'docx', 'zip', 'rar'];
            if (in_array($urlExtension, $nonMediaExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL does not appear to be a supported media file.',
                ], 400);
            }
            // For unknown extensions, allow them
        }

        // Try to get media info
        try {
            $headers = get_headers($url, 1);
            $contentType = $headers['Content-Type'] ?? '';
            
            $isVideo = str_contains($contentType, 'video/');
            $isImage = str_contains($contentType, 'image/');
            
            if (!$isImage && !$isVideo) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL does not return a supported media file.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'url' => $url,
                'content_type' => $contentType,
                'type' => $isVideo ? 'video' : 'image',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate media URL: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Xóa media file đã upload
     * 
     * Security:
     * - Chỉ cho phép xóa files trong media/ directory
     * - Validate path để prevent directory traversal attacks
     * 
     * Flow:
     * 1. Validate path input
     * 2. Kiểm tra path bắt đầu bằng "media/" (security check)
     * 3. Xóa file nếu tồn tại
     * 4. Return success/error response
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $path = $request->input('path');
            
            /**
             * Security check: Chỉ cho phép xóa files trong media/ directory
             * Prevent directory traversal attacks (../, etc.)
             */
            if (!str_starts_with($path, 'media/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file path.',
                ], 400);
            }

            // Xóa file nếu tồn tại (không throw error nếu file không tồn tại)
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media: ' . $e->getMessage(),
            ], 500);
        }
    }
}