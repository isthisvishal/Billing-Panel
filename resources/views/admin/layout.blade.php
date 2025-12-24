<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - ' . config('app.name'))</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background: var(--secondary);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            left: 0;
            top: 0;
        }

        .sidebar .brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            border-left-color: var(--primary);
            color: white;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }

        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        table thead {
            background-color: #f8f9fa;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    @yield('extra_css')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <h4 style="margin: 0;">
                <i class="fas fa-cube"></i> Admin
            </h4>
            <small style="color: #95a5a6;">{{ config('app.name') }}</small>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}" 
               href="{{ route('admin.dashboard') ?? '#' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-link {{ str_starts_with(Route::currentRouteName(), 'admin.pages') ? 'active' : '' }}" 
               href="{{ route('admin.pages.index') }}">
                <i class="fas fa-file-alt"></i> Pages
            </a>
            <a class="nav-link {{ str_starts_with(Route::currentRouteName(), 'admin.categories') ? 'active' : '' }}" 
               href="{{ route('admin.categories.index') }}">
                <i class="fas fa-layer-group"></i> Categories
            </a>
            <a class="nav-link {{ str_starts_with(Route::currentRouteName(), 'admin.plans') ? 'active' : '' }}" 
               href="{{ route('admin.plans.index') }}">
                <i class="fas fa-box"></i> Plans
            </a>
            <a class="nav-link" href="{{ url('/') }}">
                <i class="fas fa-home"></i> View Site
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
            <form method="POST" action="{{ route('logout') }}" style="padding: 12px 20px;">
                @csrf
                <button type="submit" class="nav-link" style="background: none; border: none; padding: 0; color: #ecf0f1;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Messages -->
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Content -->
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('extra_js')
</body>
</html>
