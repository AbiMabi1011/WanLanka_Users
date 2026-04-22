@extends('layouts.master')

@section('title', 'Contact Us | WanLanka')

@section('content')
<div class="contact-page-container" style="max-width:1200px; margin: 3rem auto; padding: 0 1rem;">
    <h1 class="text-center mb-5" style="font-weight: 800; color: #264653; font-size: 3rem;">Contact Us</h1>

    <!-- Branches Section -->
    <div class="branches-wrapper mb-5">
        @php
            $branches = [
                ['image' => asset('images/branch1.webp'), 'name' => 'Colombo Branch', 'description' => 'Main office in the capital heart.'],
                ['image' => asset('images/branch2.jpeg'), 'name' => 'Kandy Branch', 'description' => 'Serving the scenic hill country.'],
                ['image' => asset('images/branch3.jpg'), 'name' => 'Galle Branch', 'description' => 'Coastal services from the south.'],
                ['image' => asset('images/branch4.jpg'), 'name' => 'Jaffna Branch', 'description' => 'Your trusted partner in the north.'],
                ['image' => asset('images/branch5.jpg'), 'name' => 'Negombo Branch', 'description' => 'Strategic location near the airport.'],
            ];
        @endphp

        <div class="row g-4 justify-content-center">
            @foreach ($branches as $index => $branch)
                <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div class="branch-card-modern">
                        <div class="branch-img-h">
                            <img src="{{ $branch['image'] }}" alt="{{ $branch['name'] }}">
                        </div>
                        <div class="p-3 text-center">
                            <h6 class="fw-bold mb-1">{{ $branch['name'] }}</h6>
                            <p class="text-muted small mb-0">{{ $branch['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-container mb-5" data-aos="fade-up">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31145.937455228384!2d80.0087224852482!3d9.669487795453967!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3af5e20ad1edb62b%3A0xa456f2a6e83c080b!2sJaffna%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1691856000000!5m2!1sen!2slk"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

    <!-- Contact Form Section -->
    <div class="form-container-h mx-auto" style="max-width: 600px;" data-aos="fade-up">
        <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5">
            <h3 class="text-center mb-4" style="color: #2a9d8f; font-weight: 700;">Send Details</h3>
            <form action="{{ url('contact/send') }}" method="POST" class="d-flex flex-column gap-3">
                @csrf
                <div class="form-floating">
                    <input type="text" name="name" class="form-control rounded-3" id="name" placeholder="Full Name" required>
                    <label for="name">Full Name</label>
                </div>
                <div class="form-floating">
                    <input type="tel" name="phone" class="form-control rounded-3" id="phone" placeholder="Phone Number" required>
                    <label for="phone">Phone Number</label>
                </div>
                <div class="form-floating">
                    <input type="email" name="email" class="form-control rounded-3" id="email" placeholder="Email Address" required>
                    <label for="email">Email Address</label>
                </div>
                <div class="form-floating">
                    <textarea name="message" class="form-control rounded-3" id="message" placeholder="Your Message" style="height: 120px" required></textarea>
                    <label for="message">Your Message</label>
                </div>
                <button type="submit" class="btn btn-lg w-100 mt-2" style="background: #2a9d8f; color: white; border-radius: 10px; font-weight: 600;">
                    SEND DETAILS <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .branch-card-modern {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #eee;
    }
    .branch-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #2a9d8f;
    }
    .branch-img-h {
        height: 120px;
        overflow: hidden;
    }
    .branch-img-h img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .form-control:focus {
        border-color: #2a9d8f;
        box-shadow: 0 0 0 0.25rem rgba(42, 157, 143, 0.1);
    }
</style>
@endsection
