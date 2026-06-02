<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #2c3e50;
            color: white;
            width: 250px;
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            left: 0;
            top: 0;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-content {
            padding: 20px;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-header h5 {
            margin: 0;
            font-weight: bold;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .sidebar-header h5 {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .toggle-btn:hover {
            transform: scale(1.1);
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 0.95rem;
        }

        .sidebar a i {
            min-width: 25px;
            margin-right: 10px;
            text-align: center;
        }

        .sidebar a span {
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed a span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #34495e;
            color: #fff;
            padding-left: 20px;
        }

        .user-info {
            padding: 15px;
            border-top: 1px solid #34495e;
            margin-top: auto;
            display: flex;
            flex-direction: column;
        }

        .user-info .user-name {
            font-weight: 600;
            margin-bottom: 10px;
            color: #ecf0f1;
            font-size: 0.9rem;
            transition: opacity 0.3s ease;
            overflow: hidden;
        }

        .sidebar.collapsed .user-info .user-name {
            opacity: 0;
            height: 0;
        }

        .logout-btn {
            width: 100%;
            text-align: left;
            padding: 8px 10px;
            font-size: 0.85rem;
        }

        .sidebar.collapsed .logout-btn {
            padding: 8px 5px;
        }

        .sidebar.collapsed .logout-btn span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .main-wrapper {
            flex: 1;
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        .main-wrapper.expanded {
            margin-left: 70px;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-toggler-icon::after {
            content: '☰';
            font-size: 1.5rem;
        }

        .main-content {
            padding: 20px;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            border-radius: 8px;
        }

        .search-box {
            margin-bottom: 20px;
        }

        .search-box input {
            border-radius: 5px;
            padding: 10px 15px;
            border: 1px solid #ddd;
        }

        .search-box input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar-header h5,
            .user-info .user-name {
                opacity: 0;
                width: 0;
                overflow: hidden;
            }

            .main-wrapper {
                margin-left: 70px;
            }

            .sidebar.collapsed {
                width: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-content d-flex flex-column h-100">
                <div class="sidebar-header">
                    <h5>
                        <i class="fas fa-boxes"></i> <span>Inventory</span>
                    </h5>
                    <button class="toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>

                <nav style="flex: 1;">
                    <a href="/inventory/products" class="{{request()->routeIs('products.*') ? 'active' : ''}}">
                        <i class="fas fa-box"></i> <span>Product</span>
                    </a>
                    <a href="/inventory/materials" class="{{request()->routeIs('materials.*') ? 'active' : ''}}">
                        <i class="fas fa-wrench"></i> <span>Material</span>
                    </a>
                    <a href="/inventory/orders" class="{{request()->routeIs('orders.*') ? 'active' : ''}}">
                        <i class="fas fa-shopping-cart"></i> <span>Order</span>
                    </a>
                </nav>

                <div class="user-info">
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light logout-btn">
                            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-wrapper" id="mainWrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-warehouse"></i> Inventory Management System
                    </span>
                </div>
            </nav>

            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            const toggleBtn = document.getElementById('sidebarToggle');
            let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            // Initialize sidebar state
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainWrapper.classList.add('expanded');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            }

            toggleBtn.addEventListener('click', function() {
                isCollapsed = !isCollapsed;
                sidebar.classList.toggle('collapsed');
                mainWrapper.classList.toggle('expanded');
                
                if (isCollapsed) {
                    toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                } else {
                    toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                }

                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });
        });
    </script>
</body>
</html>
