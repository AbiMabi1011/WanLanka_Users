@section('content')
<div class="about-us-page">
<style>
    /* ====== Modern Clean Design ====== */
    .about-us-page {
        --about-primary: #2a9d8f;
        --about-secondary: #264653;
        --about-accent: #e9c46a;
        --about-text: #2c3e50;
        --about-muted: #64748b;
        --about-bg: #ffffff;
        --about-radius: 24px;
        --about-shadow: 0 10px 30px rgba(0,0,0,0.05);
        --about-transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .about-us-page {
        background: #f8fafc;
        color: var(--about-text);
        font-family: 'Outfit', sans-serif;
    }

    /* ====== Hero Section ====== */
    .team-section {
        display: flex;
        align-items: center;
        gap: 80px;
        padding: 100px 60px;
        background: var(--about-bg);
        border-radius: 0 0 100px 0;
        box-shadow: var(--about-shadow);
    }

    @media (max-width: 1100px) {
        .team-section {
            flex-direction: column;
            padding: 60px 30px;
            gap: 50px;
            text-align: center;
            border-radius: 0 0 50px 0;
        }
    }

    /* Image Container */
    .team-image-wrapper {
        flex: 1;
        position: relative;
        perspective: 1000px;
    }

    .team-image-container {
        width: 100%;
        height: 700px;
        border-radius: var(--about-radius);
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        transform: rotateY(-8deg);
        transition: var(--about-transition);
    }

    .team-image-container:hover {
        transform: rotateY(0deg) scale(1.02);
    }

    .team-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #f1f5f9;
        transition: transform 0.8s ease;
    }

    .team-image-container:hover .team-image {
        transform: scale(1.08);
    }

    /* Content Area */
    .team-content {
        flex: 1;
    }

    .badge-premium {
        display: inline-block;
        padding: 8px 20px;
        background: rgba(42, 157, 143, 0.1);
        color: var(--about-primary);
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 20px;
    }

    .team-title {
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--about-secondary);
        line-height: 1.1;
        margin-bottom: 30px;
    }

    .team-text {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--about-muted);
        margin-bottom: 25px;
    }

    /* ====== Members Section ====== */
    .members-section {
        padding: 120px 60px;
        background: transparent;
    }

    .section-header {
        text-align: center;
        margin-bottom: 80px;
    }

    .section-title {
        font-size: 3rem;
        font-weight: 800;
        color: var(--about-secondary);
        margin-bottom: 20px;
    }

    .title-underline {
        width: 100px;
        height: 5px;
        background: var(--about-primary);
        margin: 0 auto;
        border-radius: 10px;
    }

    .team-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .team-card {
        background: #ffffff;
        border-radius: var(--about-radius);
        padding: 40px 20px;
        text-align: center;
        box-shadow: var(--about-shadow);
        transition: var(--about-transition);
        border: 1px solid rgba(0,0,0,0.02);
    }

    .team-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        border-color: var(--about-primary);
    }

    .member-img-wrap {
        width: 120px;
        height: 120px;
        margin: 0 auto 25px;
        position: relative;
    }

    .member-img-wrap::after {
        content: '';
        position: absolute;
        inset: -10px;
        border: 2px dashed var(--about-primary);
        border-radius: 50%;
        opacity: 0;
        transition: var(--about-transition);
        transform: rotate(0deg);
    }

    .team-card:hover .member-img-wrap::after {
        opacity: 0.5;
        transform: rotate(180deg);
    }

    .team-card img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .team-card h3 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--about-secondary);
        margin-bottom: 8px;
    }

    .team-card p {
        font-size: 0.95rem;
        color: var(--about-primary);
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* Social Icons in Card */
    .member-socials {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        opacity: 0;
        transform: translateY(10px);
        transition: var(--about-transition);
    }

    .team-card:hover .member-socials {
        opacity: 1;
        transform: translateY(0);
    }

    .member-socials a {
        color: var(--about-muted);
        transition: color 0.3s;
    }

    .member-socials a:hover {
        color: var(--about-primary);
    }

    @media (max-width: 768px) {
        .team-title { font-size: 2.5rem; }
        .section-title { font-size: 2.2rem; }
        .members-section { padding: 60px 20px; }
    }
</style>

<!-- ====== Hero Intro ====== -->
<div class="team-section" data-aos="fade-up">
    <!-- Team Image -->
    <div class="team-image-wrapper">
        <div class="team-image-container">
            <img src="{{ asset('images/teams.jpeg') }}" alt="Our Team" class="team-image">
        </div>
    </div>

    <!-- Team Content -->
    <div class="team-content">
        <div class="badge-premium" data-aos="fade-right" data-aos-delay="200">About WanLanka</div>
        <h2 class="team-title" data-aos="fade-right" data-aos-delay="400">Voices Behind Your Journey</h2>
        <p class="team-text" data-aos="fade-right" data-aos-delay="600">
            At WanLanka, our shared passion is the beauty and mystery of Sri Lanka. Our team, based in our office on the island, has firsthand knowledge of its wonders. We’re more than just a digital presence; we’re real individuals offering profound insights into Sri Lanka’s allure.
        </p>
        <p class="team-text" data-aos="fade-right" data-aos-delay="800">
            Instead of wrestling with travel intricacies yourself, trust our experts to craft a tailor-made Sri Lankan experience for you. Whether you’re reconnecting with us or seeking specific guidance, WanLanka stands as the premier tour operator.
        </p>
    </div>
</div>

<!-- ====== Meet the Team ====== -->
<div class="members-section">
    <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Our Super Squad</h2>
        <div class="title-underline"></div>
    </div>

    <div class="team-cards">
        @php
            $members = [
                ['name' => 'J.Digevan', 'role' => 'Lead Guide', 'img' => 'nimal.jpg'],
                ['name' => 'L.S.Dorathy', 'role' => 'Travel Designer', 'img' => 'kamal.jpg'],
                ['name' => 'S.Lajithan', 'role' => 'Operations Manager', 'img' => 'suneth.jpg'],
                ['name' => 'A.Abishanan', 'role' => 'Logistics Expert', 'img' => 'ruwan.jpg'],
                ['name' => 'A.Archaga', 'role' => 'Customer Care', 'img' => 'ishara.jpg'],
            ];
        @endphp

        @foreach($members as $index => $member)
        <div class="team-card" data-aos="zoom-in" data-aos-delay="{{ $index * 100 }}">
            <div class="member-img-wrap">
                <img src="{{ asset('images/team/' . $member['img']) }}" alt="{{ $member['name'] }}">
            </div>
            <h3>{{ $member['name'] }}</h3>
            <p>{{ $member['role'] }}</p>
            <div class="member-socials">
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        @endforeach
    </div>
</div>

</div>
@endsection
