<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','WanLanka')</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- AOS (Animate On Scroll) -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Premium Custom Styles -->
  <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
  <link href="{{ asset('css/provinces.css') }}" rel="stylesheet">

  <!-- Botpress Chatbot -->
  <script src="https://cdn.botpress.cloud/webchat/v3.2/inject.js"></script>
  <script src="https://files.bpcontent.cloud/2025/09/09/18/20250909182527-FY195H05.js" defer></script>

  {{-- Component styles - only load on pages that need them --}}
  @yield('component-styles')

  {{-- Page-specific styles --}}
  @yield('styles')
</head>
<body class="bg-gradient-animate">
  <div id="scroll-progress"></div>

  @include('include.header')

  <main>
    @yield('content')
  </main>

  @include('include.footer')

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  
  <script>
    // Initialize AOS
    AOS.init({
      duration: 1000,
      once: false,
      mirror: true
    });

    // Scroll Progress
    window.onscroll = function() {
      let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
      let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      let scrolled = (winScroll / height) * 100;
      document.getElementById("scroll-progress").style.width = scrolled + "%";
    };
  </script>

  <script src="{{ asset('js/home-slider.js') }}"></script>

  {{-- Component scripts --}}
  @yield('component-scripts')

  {{-- Page-specific scripts --}}
  @yield('scripts')
</body>
</html>
