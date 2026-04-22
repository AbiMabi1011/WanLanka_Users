<div class="c">
    <input type="radio" name="a" id="cr-1" checked>
    <label for="cr-1"></label>
    <div class="ci active">
        <h2 class="ch" data-aos="fade-up" data-aos-delay="100">What do you know?</h2>
        <div class="img-3d-container">
            <img src="{{ asset('images/slider-image-1.jpg') }}" alt="Snow on leafs">
        </div>
    </div>

    <input type="radio" name="a" id="cr-2">
    <label for="cr-2"></label>
    <div class="ci">
        <h2 class="ch" data-aos="fade-up">Look from inside?</h2>
        <div class="img-3d-container">
            <img src="{{ asset('images/slider-image-2.jpg') }}" alt="Trees">
        </div>
    </div>

    <input type="radio" name="a" id="cr-3">
    <label for="cr-3"></label>
    <div class="ci">
        <h2 class="ch" data-aos="fade-up">In the mountains?</h2>
        <div class="img-3d-container">
            <img src="{{ asset('images/slider-image-3.jpg') }}" alt="Mountains and houses">
        </div>
    </div>

    <input type="radio" name="a" id="cr-4">
    <label for="cr-4"></label>
    <div class="ci">
        <h2 class="ch" data-aos="fade-up">Above looks beautiful?</h2>
        <div class="img-3d-container">
            <img src="{{ asset('images/slider-image-4.jpg') }}" alt="Sky and mountains">
        </div>
    </div>

    <div class="slider-nav">
        <label for="cr-1"></label>
        <label for="cr-2"></label>
        <label for="cr-3"></label>
        <label for="cr-4"></label>
    </div>
    
    <!-- Scroll Down Indicator -->
    <div class="scroll-down">
        <i class="fas fa-chevron-down"></i>
    </div>
</div>

<style>
.slider-subtitle {
    position: absolute;
    bottom: 25%;
    left: 5%;
    font-family: 'Poppins', sans-serif;
    font-size: 1.5rem;
    color: #fff;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    z-index: 3;
    max-width: 600px;
    line-height: 1.4;
}

.slider-btn {
    position: absolute;
    bottom: 15%;
    left: 5%;
    z-index: 3;
}

.explore-btn {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 15px 35px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.explore-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
}

@media (max-width: 768px) {
    .ch {
        font-size: 2rem !important;
        bottom: 30% !important;
    }
    
    .slider-subtitle {
        font-size: 1.1rem;
        bottom: 22%;
        max-width: 90%;
    }
    
    .slider-btn {
        bottom: 12%;
    }
    
    .explore-btn {
        padding: 12px 25px;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .ch {
        font-size: 1.6rem !important;
        bottom: 35% !important;
    }
    
    .slider-subtitle {
        font-size: 0.9rem;
        bottom: 25%;
    }
}
</style>