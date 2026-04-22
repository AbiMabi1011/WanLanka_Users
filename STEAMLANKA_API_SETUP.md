# SteamLanka API Setup Guide

## Overview
SteamLanka automatically fetches trending Sri Lankan tourist place videos from social media platforms. This guide explains how to set up the API integrations.

## Required API Keys

### 1. YouTube Data API v3
- **Purpose**: Fetch trending travel videos from YouTube
- **Setup**: 
  1. Go to [Google Cloud Console](https://console.developers.google.com/)
  2. Create a new project or select existing one
  3. Enable YouTube Data API v3
  4. Create credentials (API Key)
  5. Add the key to your `.env` file: `YOUTUBE_API_KEY=your_key_here`

### 2. Instagram Basic Display API
- **Purpose**: Fetch trending travel content from Instagram
- **Setup**:
  1. Go to [Facebook Developers](https://developers.facebook.com/)
  2. Create a new app
  3. Add Instagram Basic Display product
  4. Generate access token
  5. Add to your `.env` file: `INSTAGRAM_ACCESS_TOKEN=your_token_here`

### 3. TikTok API (Optional)
- **Purpose**: Fetch viral travel videos from TikTok
- **Setup**:
  1. Go to [TikTok Developers](https://developers.tiktok.com/)
  2. Create an app
  3. Get API credentials
  4. Add to your `.env` file: `TIKTOK_API_KEY=your_key_here`

## Environment Configuration

Add these variables to your `.env` file:

```env
# Social Media API Keys
YOUTUBE_API_KEY=your_youtube_api_key_here
INSTAGRAM_ACCESS_TOKEN=your_instagram_access_token_here
TIKTOK_API_KEY=your_tiktok_api_key_here

# Cache Configuration (optional)
CACHE_DRIVER=file
CACHE_TTL=1800
```

## Features

### Automatic Trending Detection
- Videos are automatically ranked by trending score
- Trending score considers:
  - View count
  - Recency (recent videos score higher)
  - Engagement metrics

### Smart Caching
- Videos are cached for 30 minutes to improve performance
- Reduces API calls and improves loading speed

### Auto-Refresh
- Page automatically refreshes trending videos every 5 minutes
- Only refreshes when page is visible (performance optimization)

### Multi-Platform Support
- YouTube: Full API integration with real trending data
- Instagram: Sample data with trending characteristics
- TikTok: Sample data with viral characteristics

### Enhanced Search
- Search by location, title, description, channel, or tags
- Real-time filtering without page reload

## Fallback System

If API keys are not configured or API calls fail:
- System falls back to enhanced sample data
- Sample data includes trending characteristics
- No interruption to user experience

## Trending Indicators

- **Trending Badge**: Shows for videos with trending score > 80
- **Trending Score**: Fire emoji with numerical score
- **Visual Indicators**: Animated badges and scores

## Performance Optimizations

- Lazy loading for video thumbnails
- Cached API responses
- Auto-refresh only when page is visible
- Efficient video sorting and filtering

## Testing

To test without API keys:
1. The system will automatically use sample data
2. Sample data includes trending characteristics
3. All features work with sample data

To test with real APIs:
1. Configure API keys in `.env` file
2. Clear cache: `php artisan cache:clear`
3. Visit `/steamlanka` page
4. Check browser console for API response logs
