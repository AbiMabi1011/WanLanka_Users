@extends('layouts.app')

@section('title', 'Test Video Upload')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Test Video Upload</h3>
                </div>
                <div class="card-body">
                    <form id="testUploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="video" class="form-label">Select Video File (Max 50MB)</label>
                            <input type="file" class="form-control" id="video" name="video" accept="video/*" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="Test Video Upload" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">This is a test video upload</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" value="Colombo, Sri Lanka" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hashtags" class="form-label">Hashtags</label>
                            <input type="text" class="form-control" id="hashtags" name="hashtags" value="#test #srilanka #travel">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn">Upload Video</button>
                    </form>
                    
                    <div id="result" class="mt-4" style="display: none;">
                        <h4>Upload Result:</h4>
                        <pre id="resultContent"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('testUploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const videoFile = document.getElementById('video').files[0];
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const location = document.getElementById('location').value;
    const hashtags = document.getElementById('hashtags').value;
    
    // Get UI elements
    const submitBtn = document.getElementById('submitBtn');
    const resultDiv = document.getElementById('result');
    const resultContent = document.getElementById('resultContent');
    
    // Validation
    if (!videoFile) {
        alert('Please select a video file');
        return;
    }
    
    // Disable submit button and show loading state
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Uploading...';
    submitBtn.disabled = true;
    
    // Add form data
    formData.append('video', videoFile);
    formData.append('title', title);
    formData.append('description', description);
    formData.append('location', location);
    formData.append('hashtags', hashtags);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    try {
        const response = await fetch('/api/upload-video', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        // Show result
        resultDiv.style.display = 'block';
        resultContent.textContent = JSON.stringify(data, null, 2);
        
        if (data.success) {
            alert('Video uploaded successfully!');
        } else {
            let errorMessage = data.message || 'Unknown error';
            if (data.errors) {
                errorMessage += '\n\nValidation errors:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `- ${key}: ${data.errors[key].join(', ')}\n`;
                });
            }
            alert('Error uploading video: ' + errorMessage);
        }
    } catch (error) {
        console.error('Upload error:', error);
        resultDiv.style.display = 'block';
        resultContent.textContent = 'Network error: ' + error.message;
        alert('Network error uploading video. Please check your connection and try again.');
    } finally {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
});
</script>
@endsection