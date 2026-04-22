@extends('layouts.master')

@section('title', 'Share Your Gem | WanLanka')

@section('content')
<div class="add-place-page bg-gradient-animate py-5">
    <div class="container" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass p-4 p-md-5 rounded-4 border-accent" data-aos="zoom-in">
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <h1 class="text-neon mb-3">✨ Share Your Travel Gem</h1>
                        <p class="lead text-white-50">Inspire fellow travelers with extraordinary destinations</p>
                    </div>

                    <!-- Form -->
                    <form id="addPlaceForm">
                        <div class="row g-4">
                            <!-- Place Name -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control glass" id="placeName" name="place_name" placeholder="Place Name" required>
                                    <label for="placeName" class="text-white">Place Name</label>
                                </div>
                            </div>
                            <!-- Google Maps Link -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="url" class="form-control glass" id="googleLink" name="google_map_link" placeholder="Google Maps Link" required>
                                    <label for="googleLink" class="text-white">Google Maps Link</label>
                                </div>
                            </div>
                            <!-- Province -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select glass" id="province" name="province" required>
                                        <option selected disabled>Select Province</option>
                                        <option value="Western">Western</option>
                                        <option value="Central">Central</option>
                                        <option value="Southern">Southern</option>
                                        <option value="Northern">Northern</option>
                                        <option value="Eastern">Eastern</option>
                                        <option value="North Western">North Western</option>
                                        <option value="North Central">North Central</option>
                                        <option value="Uva">Uva</option>
                                        <option value="Sabaragamuwa">Sabaragamuwa</option>
                                    </select>
                                    <label for="province" class="text-white">Province</label>
                                </div>
                            </div>
                            <!-- District -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select glass" id="district" name="district" required>
                                        <option selected disabled>Select District</option>
                                    </select>
                                    <label for="district" class="text-white">District</label>
                                </div>
                            </div>
                            <!-- Location -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control glass" id="location" name="location" placeholder="Location" required>
                                    <label for="location" class="text-white">Location</label>
                                </div>
                            </div>
                            <!-- Nearest City -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control glass" id="nearestCity" name="nearest_city" placeholder="Nearest City" required>
                                    <label for="nearestCity" class="text-white">Nearest City</label>
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control glass" id="description" name="description" placeholder="Description" style="height:120px;" required></textarea>
                                    <label for="description" class="text-white">Description</label>
                                </div>
                            </div>
                            <!-- Best Suited For -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select glass" id="bestSuited" name="best_suited_for" required>
                                        <option selected disabled>Best Suited For</option>
                                        <option value="Families">Families</option>
                                        <option value="Friends">Friends</option>
                                        <option value="Solo Travelers">Solo Travelers</option>
                                        <option value="Adventure Seekers">Adventure Seekers</option>
                                        <option value="Romantic Getaways">Romantic Getaways</option>
                                    </select>
                                    <label for="bestSuited" class="text-white">Best Suited For</label>
                                </div>
                            </div>
                            <!-- Your Rating -->
                            <div class="col-md-6">
                                <label class="form-label mb-2 text-white">Your Rating (1–5)</label>
                                <div class="d-flex align-items-center">
                                    <input type="range" class="form-range flex-grow-1" min="1" max="5" id="rating" name="rating" required>
                                    <span id="ratingValue" class="ms-3 badge bg-accent fw-bold p-2">3</span>
                                </div>
                            </div>
                            <!-- Upload Image -->
                            <div class="col-md-12">
                                <label class="form-label text-white">Upload Image</label>
                                <input type="file" class="form-control glass" id="uploadImage" name="image" accept="image/*">
                            </div>
                            <!-- Submit Button -->
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-premium btn-lg px-5 py-3">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Destination
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control.glass, .form-select.glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
    }
    .form-control.glass:focus, .form-select.glass:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #635BFF;
        color: white;
        box-shadow: 0 0 15px rgba(99, 91, 255, 0.3);
    }
    .form-floating label { color: rgba(255,255,255,0.7) !important; }
    .form-select.glass option { background: #1a1a2e; color: white; }
    .bg-accent { background-color: #635BFF; }
    .border-accent { border: 1px solid rgba(99, 91, 255, 0.3); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update rating display
        const rating = document.getElementById('rating');
        const ratingValue = document.getElementById('ratingValue');
        if(rating) {
            rating.addEventListener('input', function() {
                ratingValue.textContent = this.value;
            });
        }

        // District data based on province
        const districtsByProvince = {
            'Western': ['Colombo', 'Gampaha', 'Kalutara'],
            'Central': ['Kandy', 'Matale', 'Nuwara Eliya'],
            'Southern': ['Galle', 'Matara', 'Hambantota'],
            'Northern': ['Jaffna', 'Mannar', 'Vavuniya', 'Mullaitivu', 'Kilinochchi'],
            'Eastern': ['Batticaloa', 'Ampara', 'Trincomalee'],
            'North Western': ['Kurunegala', 'Puttalam'],
            'North Central': ['Anuradhapura', 'Polonnaruwa'],
            'Uva': ['Badulla', 'Monaragala'],
            'Sabaragamuwa': ['Ratnapura', 'Kegalle']
        };

        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');

        if(provinceSelect) {
            provinceSelect.addEventListener('change', function () {
                const province = this.value;
                districtSelect.innerHTML = '<option selected disabled>Select District</option>';
                if (districtsByProvince[province]) {
                    districtsByProvince[province].forEach(district => {
                        const option = document.createElement('option');
                        option.value = district;
                        option.textContent = district;
                        districtSelect.appendChild(option);
                    });
                }
            });
        }

        // Form submission
        const form = document.getElementById('addPlaceForm');
        if(form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to submit this place?')) return;

                const formData = new FormData(this);

                @auth
                    formData.append('user_id', '{{ auth()->id() }}');
                    formData.append('user_name', '{{ auth()->user()->name }}');
                    formData.append('user_email', '{{ auth()->user()->email }}');
                @else
                    alert('You must be logged in to submit a place.');
                    return;
                @endauth

                try {
                    const response = await fetch('http://127.0.0.1:8000/api/new-place', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer 1|8OImmqdUzzCwAOzoksoHFeOjpz1iSWSLTbTL3geC43aa48db'
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        throw new Error(err.message || `Submission failed (HTTP ${response.status})`);
                    }

                    const data = await response.json();
                    alert('Place submitted successfully!');
                    this.reset();
                    districtSelect.innerHTML = '<option selected disabled>Select District</option>';

                } catch (error) {
                    console.error('Error submitting place:', error);
                    alert('Error submitting place: ' + error.message);
                }
            });
        }
    });
</script>
@endsection
