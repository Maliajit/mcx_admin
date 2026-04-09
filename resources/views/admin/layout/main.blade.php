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
    <script>
        // Automatic Limit Order Processing Heartbeat
        // Process hits every 2 seconds while admin is active
        setInterval(function() {
            fetch("{{ route('orders.process-limits') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.processed_count > 0) {
                        console.log('Automated Trade Hit: ' + data.processed_count + ' orders confirmed.');
                        // Optionally refresh the current page if it's the pending list
                        if (window.location.href.indexOf('orders/pending') > -1) {
                             window.location.reload();
                        }
                    }
                })
                .catch(err => console.error('Heartbeat failed:', err));
        }, 2000);
    </script>
    @stack('js')
</body>
</html>
