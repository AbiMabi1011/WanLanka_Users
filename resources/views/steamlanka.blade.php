@extends('layouts.master')

@section('title', 'SteamLanka | Discovery Feed')

@section('content')
<style>
    /* Reset & TikTok Layout */
    #main-content { padding-top: 0 !important; margin: 0 !important; overflow: hidden; }
    
    .steamlanka-wrapper {
        position: fixed;
        top: 70px;
        left: 0;
        width: 100%;
        height: calc(100vh - 70px);
        background: #000;
        z-index: 100;
    }

    .video-feed {
        height: 100%;
        overflow-y: scroll;
        scroll-snap-type: y mandatory;
        scrollbar-width: none;
    }
    .video-feed::-webkit-scrollbar { display: none; }

    .video-card {
        height: 100%;
        width: 100%;
        scroll-snap-align: start;
        position: relative;
        display: flex;
        justify-content: center;
        background: #000;
    }

    .video-player-container {
        width: 100%;
        max-width: 450px;
        height: 100%;
        position: relative;
        background: #111;
    }

    .video-player-container video, .video-player-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        border: none;
    }

    /* TikTok UI Elements */
    .video-info {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 40px 20px;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        pointer-events: none;
        z-index: 5;
    }

    .place-tag {
        display: inline-flex;
        align-items: center;
        background: var(--primary, #2a9d8f);
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 12px;
        pointer-events: auto;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .video-actions {
        position: absolute;
        right: 15px;
        bottom: 80px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        z-index: 10;
    }

    .action-btn {
        background: rgba(255,255,255,0.1);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(15px);
        transition: all 0.2s;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .action-btn:hover { background: rgba(255,255,255,0.2); transform: scale(1.05); }
    .action-btn.active { color: var(--primary, #2a9d8f); }
    .action-btn.liked.active { color: #ff0050; }
    
    .btn-label { font-size: 0.6rem; margin-top: 3px; font-weight: 600; }

    /* Upload Floating Button - MOVED TO LEFT TO AVOID CHATBOT */
    .upload-float-btn {
        position: fixed;
        bottom: 30px;
        left: 30px; /* Changed from right to left */
        width: 70px;
        height: 70px;
        background: var(--primary, #2a9d8f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        box-shadow: 0 10px 25px rgba(42, 157, 143, 0.6);
        z-index: 10001; /* Very high z-index */
        cursor: pointer;
        transition: all 0.3s;
        border: 4px solid white;
        animation: pulse-green 2s infinite;
    }
    
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(42, 157, 143, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(42, 157, 143, 0); }
        100% { box-shadow: 0 0 0 0 rgba(42, 157, 143, 0); }
    }
    
    .upload-float-btn:hover { transform: translateY(-5px) scale(1.1); background: #1b7a6d; }

    /* Modal Styling */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.85);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        backdrop-filter: blur(10px);
    }
    .upload-modal {
        background: white;
        border-radius: 25px;
        width: 90%;
        max-width: 500px;
        padding: 30px;
        position: relative;
    }

    /* Feed Header with Refresh */
    .feed-header {
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 1000;
        pointer-events: none;
    }
    
    .feed-header > * { pointer-events: auto; }

    .refresh-btn {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        padding: 8px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="steamlanka-wrapper">
    <!-- Feed Header -->
    <div class="feed-header">
        <div class="place-tag mb-0">
            <i class="fas fa-compass me-2"></i> WANLANKA DISCOVER
        </div>
        <button class="refresh-btn" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i> Refresh Feed
        </button>
    </div>

    <!-- TikTok Feed -->
    <div class="video-feed" id="videoFeed">
        <div class="no-videos-state" id="loadingText">
            <div class="spinner-border text-primary mb-3"></div>
            <p class="text-white">Loading community adventures...</p>
        </div>
    </div>

    <!-- Floating Upload Button -->
    <div class="upload-float-btn" onclick="openUploadModal()" title="Upload your travel video">
        <i class="fas fa-plus"></i>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="modal-overlay">
    <div class="upload-modal">
        <button class="btn-close float-end" onclick="closeUploadModal()"></button>
        <h3 class="fw-bold mb-4">Post Adventure</h3>
        
        <form id="uploadForm">
            <div class="mb-3">
                <label class="form-label fw-bold">Source Type</label>
                <div class="btn-group w-100 mb-2" role="group">
                    <input type="radio" class="btn-check" name="sourceType" id="typeFile" value="file" checked>
                    <label class="btn btn-outline-primary" for="typeFile">Upload File</label>
                    
                    <input type="radio" class="btn-check" name="sourceType" id="typeLink" value="link">
                    <label class="btn btn-outline-primary" for="typeLink">Social Link</label>
                </div>
            </div>

            <!-- File Input -->
            <div class="mb-3" id="fileInputContainer">
                <label class="form-label fw-bold">Select Video File</label>
                <input type="file" id="videoFile" class="form-control rounded-pill" accept="video/*">
                <div class="form-text">Max 50MB. Vertical recommended.</div>
            </div>

            <!-- Link Input -->
            <div class="mb-3 d-none" id="linkInputContainer">
                <label class="form-label fw-bold">Social Media Link</label>
                <input type="url" id="socialLink" class="form-control rounded-pill" placeholder="https://youtube.com/shorts/... or Instagram Reel">
                <div class="form-text text-primary"><i class="fas fa-info-circle me-1"></i> Supports YouTube Shorts & Instagram Reels</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Place Name</label>
                <input type="text" id="placeName" class="form-control rounded-pill" placeholder="Where was this adventure?" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Caption</label>
                <textarea id="caption" class="form-control rounded-4" rows="2" placeholder="Share the experience..."></textarea>
            </div>

            <div class="progress mb-3 d-none" id="uploadProgress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-lg">
                <i class="fas fa-paper-plane me-2"></i>Post Video
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videoFeed = document.getElementById('videoFeed');
        let videos = [];

        function createVideoCard(v) {
            let mediaContent = '';
            
            // Enhanced social media detector
            if (v.src.includes('youtube.com') || v.src.includes('youtu.be')) {
                const id = v.src.split('v=')[1]?.split('&')[0] || v.src.split('/').pop();
                mediaContent = `<iframe src="https://www.youtube.com/embed/${id}?autoplay=0&mute=1&loop=1&playlist=${id}&controls=0" allow="autoplay; encrypted-media"></iframe>`;
            } else if (v.src.includes('instagram.com')) {
                const cleanUrl = v.src.split('?')[0].replace(/\/$/, "");
                mediaContent = `<iframe src="${cleanUrl}/embed" frameborder="0" scrolling="no"></iframe>`;
            } else if (v.src.includes('tiktok.com')) {
                // Handle full TikTok links (already resolved by backend)
                let id = '';
                if (v.src.includes('/video/')) {
                    const parts = v.src.split('/video/');
                    id = parts[1].split('?')[0].split('/')[0]; // Robust ID extraction
                }
                
                if (id && !isNaN(id)) {
                    mediaContent = `<iframe src="https://www.tiktok.com/embed/v2/${id}" allow="autoplay; encrypted-media"></iframe>`;
                } else {
                    // Final fallback
                    mediaContent = `<div class="d-flex align-items-center justify-content-center h-100 bg-dark text-white p-4 text-center">
                        <div><i class="fab fa-tiktok fa-3x mb-3"></i><p>Loading TikTok Adventure...</p></div>
                    </div>`;
                }
            } else {
                mediaContent = `<video src="${v.src}" loop muted playsinline></video>`;
            }

            return `
                <div class="video-card">
                    <div class="video-player-container">
                        ${mediaContent}
                        
                        <div class="video-info">
                            <div class="place-tag"><i class="fas fa-map-marker-alt me-2"></i>${v.place}</div>
                            <h5 class="fw-bold mb-1">@${v.user}</h5>
                            <p class="small mb-0 opacity-75">${v.caption || 'Exploring the beauty of ' + v.place}</p>
                        </div>

                        <div class="video-actions">
                            <!-- Like -->
                            <button class="action-btn" onclick="toggleLike(this)">
                                <i class="fas fa-heart"></i>
                                <span class="btn-label">${v.likes || 0}</span>
                            </button>
                            
                            <!-- Save -->
                            <button class="action-btn" onclick="toggleSave(this)">
                                <i class="fas fa-bookmark"></i>
                                <span class="btn-label">Save</span>
                            </button>

                            <!-- Download -->
                            <a href="${v.src}" download="WanLanka_${v.place}.mp4" class="action-btn text-decoration-none">
                                <i class="fas fa-download"></i>
                                <span class="btn-label">Get</span>
                            </a>

                            <!-- Share -->
                            <button class="action-btn" onclick="shareVideo('${v.src}', '${v.place}')">
                                <i class="fas fa-share"></i>
                                <span class="btn-label">Share</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        async function loadVideos() {
            try {
                const response = await fetch('/api/sri-lanka-videos');
                const data = await response.json();
                
                if (data.success && data.videos.length > 0) {
                    videos = data.videos.map(v => ({
                        id: v.id,
                        src: v.video_url,
                        place: v.location,
                        user: v.username,
                        caption: v.description,
                        likes: v.likes_count || 0
                    }));
                    renderFeed();
                } else {
                    showEmptyState();
                }
            } catch (error) {
                showEmptyState();
            }
        }

        function renderFeed() {
            if(videos.length === 0) {
                showEmptyState();
                return;
            }
            videoFeed.innerHTML = videos.map(v => createVideoCard(v)).join('');
            setupVideoObservers();
        }

        function showEmptyState() {
            videoFeed.innerHTML = `
                <div class="no-videos-state">
                    <i class="fas fa-video-slash fa-3x mb-3 text-white-50"></i>
                    <h4 class="text-white">No community videos yet</h4>
                    <p class="text-white-50">Be the first to share an adventure!</p>
                </div>
            `;
        }

        function setupVideoObservers() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const video = entry.target.querySelector('video');
                    if (video) {
                        if (entry.isIntersecting) {
                            video.play().catch(e => console.log('Autoplay blocked'));
                        } else {
                            video.pause();
                        }
                    }
                });
            }, { threshold: 0.7 });

            document.querySelectorAll('.video-card').forEach(card => observer.observe(card));
        }

        // Global Actions
        window.toggleLike = (btn) => {
            btn.classList.toggle('liked');
            btn.classList.toggle('active');
            let count = parseInt(btn.querySelector('.btn-label').innerText);
            btn.querySelector('.btn-label').innerText = btn.classList.contains('active') ? count + 1 : count - 1;
        };

        window.toggleSave = (btn) => {
            btn.classList.toggle('active');
            btn.querySelector('.btn-label').innerText = btn.classList.contains('active') ? 'Saved' : 'Save';
        };

        window.shareVideo = async (url, place) => {
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: 'Check out this place in Sri Lanka!',
                        text: `Watch this video of ${place} on WanLanka SteamLanka`,
                        url: window.location.href
                    });
                } catch (err) {}
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Link copied to clipboard!');
            }
        };

        // Modal Controls
        window.openUploadModal = () => document.getElementById('uploadModal').style.display = 'flex';
        window.closeUploadModal = () => document.getElementById('uploadModal').style.display = 'none';

        // Toggle Inputs based on Source Type
        document.querySelectorAll('input[name="sourceType"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'file') {
                    document.getElementById('fileInputContainer').classList.remove('d-none');
                    document.getElementById('linkInputContainer').classList.add('d-none');
                    document.getElementById('videoFile').required = true;
                    document.getElementById('socialLink').required = false;
                } else {
                    document.getElementById('fileInputContainer').classList.add('d-none');
                    document.getElementById('linkInputContainer').classList.remove('d-none');
                    document.getElementById('videoFile').required = false;
                    document.getElementById('socialLink').required = true;
                }
            });
        });

        // Form Submission
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const progress = document.getElementById('uploadProgress');
            const bar = progress.querySelector('.progress-bar');
            const sourceType = document.querySelector('input[name="sourceType"]:checked').value;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Posting...';
            progress.classList.remove('d-none');

            const formData = new FormData();
            if (sourceType === 'file') {
                const file = document.getElementById('videoFile').files[0];
                if(file) formData.append('video', file);
            } else {
                const link = document.getElementById('socialLink').value;
                if(link) formData.append('social_link', link);
            }
            
            formData.append('location', document.getElementById('placeName').value);
            formData.append('title', 'Adventure at ' + document.getElementById('placeName').value);
            formData.append('description', document.getElementById('caption').value);

            try {
                // Send to backend
                const response = await fetch('/api/upload-video', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const res = await response.json();
                
                if (res.success) {
                    alert('Adventure shared successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Post Video';
                }
            } catch (err) {
                alert('Connection error. Try again.');
                btn.disabled = false;
            }
        });

        loadVideos();
        document.body.style.overflow = 'hidden';
    });
</script>
@endsection