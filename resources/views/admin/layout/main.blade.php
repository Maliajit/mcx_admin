<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - MCX Classic Bullion</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('css')
</head>
<body>
    <div class="wrapper">
        @include('admin.layout.sidebar')
        
        <div class="main-content">
            @include('admin.layout.navbar')
            
            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('js')
</body>
</html>
