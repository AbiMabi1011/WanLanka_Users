@extends('layouts.app')

@section('title', 'Test Videos')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1>Test Videos Display</h1>
            <button id="loadVideos" class="btn btn-primary">Load Videos</button>
            <div id="videoList" class="mt-4"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loadVideos').addEventListener('click', async function() {
        try {
            const response = await fetch('/api/sri-lanka-videos');
            const data = await response.json();
            
            console.log('API Response:', data);
            
            const videoList = document.getElementById('videoList');
            videoList.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            
            if (data.success && data.videos.length > 0) {
                let html = '<div class="row">';
                data.videos.forEach(video => {
                    html += `
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">${video.title}</h5>
                                    <p class="card-text">${video.description}</p>
                                    <p class="card-text"><small class="text-muted">${video.location}</small></p>
                                    <a href="${video.video_url}" target="_blank" class="btn btn-primary">View Video</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                videoList.innerHTML += html;
            }
        } catch (error) {
            console.error('Error loading videos:', error);
            document.getElementById('videoList').innerHTML = '<p class="text-danger">Error loading videos: ' + error.message + '</p>';
        }
    });
});
</script>
@endsection