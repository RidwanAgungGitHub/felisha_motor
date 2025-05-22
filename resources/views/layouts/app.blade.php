<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Inventaris - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #343a40;
            color: #fff;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            margin: 0.2rem 0;
            border-radius: 0.25rem;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        .content {
            padding: 20px;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .card-dashboard {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
        }

        .role-badge {
            font-size: 0.75rem;
            background-color: #28a745;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
        }

        .role-badge.kasir {
            background-color: #17a2b8;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="py-4 px-3 mb-4">
                    <div class="media d-flex align-items-center">
                        <div class="media-body">
                            <h4 class="m-0">Falisa Inventory</h4>
                            <small class="text-muted">
                                {{ Auth::user()->name }}
                                <span class="role-badge {{ Auth::user()->role == 'kasir' ? 'kasir' : '' }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
                <ul class="nav flex-column">
                    @if (Auth::user()->role == 'admin')
                        <!-- Dashboard - Hanya untuk Admin -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <!-- Menu khusus Admin -->
                        <li class="nav-item">
                            <a href="{{ route('barang.index') }}"
                                class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                                <i class="fas fa-box"></i> Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('barang_masuk.index') }}"
                                class="nav-link {{ request()->routeIs('barang_masuk.*') ? 'active' : '' }}">
                                <i class="fas fa-truck-loading"></i> Barang Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('barang-keluar.index') }}"
                                class="nav-link {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
                                <i class="fas fa-shipping-fast"></i> Barang Keluar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reorder-point.index') }}"
                                class="nav-link {{ request()->routeIs('reorder-point.*') ? 'active' : '' }}">
                                <i class="fas fa-exclamation-triangle"></i> Reorder Point
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('safety-stock.index') }}"
                                class="nav-link {{ request()->routeIs('safety-stock.*') ? 'active' : '' }}">
                                <i class="fas fa-shield-alt"></i> Safety Stock
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.inventory-status') }}"
                                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i> Report Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('supplier.index') }}"
                                class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                                <i class="fas fa-handshake"></i> Supplier
                            </a>
                        </li>
                    @elseif(Auth::user()->role == 'kasir')
                        <!-- Menu khusus Kasir - Langsung fokus ke kasir -->
                        <li class="nav-item">
                            <a href="{{ route('kasir') }}"
                                class="nav-link {{ request()->routeIs('kasir*') ? 'active' : '' }}">
                                <i class="fas fa-cash-register"></i> Kasir
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 shadow-sm rounded">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                            <ul class="navbar-nav">
                                @if (Auth::user()->role == 'admin')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-home"></i> Home
                                        </a>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('kasir') }}">
                                            <i class="fas fa-cash-register"></i> Home
                                        </a>
                                    </li>
                                @endif
                            </ul>
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
                                        role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                                        <small class="text-muted">({{ ucfirst(Auth::user()->role) }})</small>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog"></i>
                                                Profile</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-sign-out-alt"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Auto hide alerts after 5 seconds -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>

    @yield('scripts')
</body>

</html>
