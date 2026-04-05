<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Student Staff Manager') }} - @yield('title')</title>

    <!-- Google Analytics -->
    @php
        $gaMeasurementId = env('GOOGLE_ANALYTICS_ID', 'G-XXXXXXXXXX'); // Replace with your ID or set in .env
    @endphp
    
    @if($gaMeasurementId && $gaMeasurementId !== 'G-XXXXXXXXXX')
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaMeasurementId }}');
        
        // Optional: Track authenticated user
        @auth
        gtag('set', {'user_id': '{{ Auth::id() }}'});
        @endauth
    </script>
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            overflow-x: hidden;
        }
        
        /* Flexbox layout for sidebar + main content */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        
        /* Sidebar - fixed width, no shrink */
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 10;
        }
        
        /* Main content area - takes remaining space, handles overflow */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            min-width: 0;  /* Prevents flex overflow */
            overflow-x: auto;
        }
        
        /* Inner content wrapper with padding */
        .content-inner {
            padding: 1.5rem;
            flex: 1;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
        }
        
        /* Responsive sidebar for smaller screens */
        @media (max-width: 768px) {
            .sidebar {
                width: 240px;
            }
        }
        
        /* Nav links styling */
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            border-left: 3px solid #ffd700;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.9);
            width: 100%;
            text-align: left;
            padding: 12px 20px;
            margin-top: 20px;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .navbar-top {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.75rem 1.5rem;
            flex-shrink: 0;
        }
        
        .alert {
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Ensure tables are responsive */
        .table-responsive-custom {
            overflow-x: auto;
            width: 100%;
        }
        
        /* DataTable wrapper fixes */
        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }
        
        .dataTables_scroll {
            overflow-x: auto !important;
        }
        
        /* Card and table fixes */
        .card {
            width: 100%;
            min-width: 0;
        }
        
        table.dataTable {
            width: 100% !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="p-4">
                <h4 class="text-white mb-4">
                    <i class="fas fa-school"></i> Student Staff Manager
                </h4>
                
                <nav class="nav flex-column">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i> Students
                    </a>
                    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-user"></i> Staff
                    </a>
                    <a href="{{ route('import.form') }}" class="nav-link {{ request()->routeIs('import.*') ? 'active' : '' }}">
                        <i class="fas fa-upload"></i> Import Students
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="logout-btn nav-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <nav class="navbar-top">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@yield('title')</h5>
                    <div>
                        <span class="text-muted">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </span>
                    </div>
                </div>
            </nav>
            
            <div class="content-inner">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Ensure DataTables responsive on window resize
        $(window).on('resize', function() {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    </script>
    
    @stack('scripts')
</body>
</html>