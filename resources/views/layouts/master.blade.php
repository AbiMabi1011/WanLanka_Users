<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WanLanka')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Premium Core Styles -->
    <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
    <link href="{{ asset('css/provinces.css') }}" rel="stylesheet">

    {{-- Botpress Chatbot --}}
    <script src="https://cdn.botpress.cloud/webchat/v3.2/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/09/09/18/20250909182527-FY195H05.js" defer></script>

    @yield('styles')
    @yield('component-styles')
</head>
<body class="bg-gradient-animate">
    <div id="scroll-progress"></div>

    {{-- Manual includes as per "Old Layout" preference --}}
    @include('include.header')

    <main id="main-content" style="padding-top: 20px;">
        @yield('content')
    </main>

    @include('include.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/home-slider.js') }}"></script>
    
    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: false,
                mirror: true,
                offset: 120
            });

            // Scroll Progress
            window.addEventListener('scroll', function() {
                let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                let scrolled = (winScroll / height) * 100;
                let progressBar = document.getElementById("scroll-progress");
                if (progressBar) progressBar.style.width = scrolled + "%";
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
