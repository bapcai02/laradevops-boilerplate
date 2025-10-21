<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DevOps Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-dark: #5f61e6;
            --primary-light: #7c7dff;
            --secondary-color: #8592a3;
            --success-color: #71dd37;
            --warning-color: #ffab00;
            --danger-color: #ff3e1d;
            --info-color: #03c3ec;
            --light-bg: #f5f5f9;
            --card-bg: #ffffff;
            --text-primary: #566a7f;
            --text-secondary: #8592a3;
            --text-muted: #a1acb8;
            --border-color: #d9dee3;
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--light-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-primary);
            font-weight: 400;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--sidebar-bg);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
        }

        .sidebar-logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.125rem;
        }

        .sidebar-logo-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .sidebar-logo-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: -0.25rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1.25rem;
            margin-bottom: 0.5rem;
        }

        .nav-item {
            margin: 0.125rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            position: relative;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(105, 108, 255, 0.08);
            color: var(--primary-color);
        }

        .nav-link.active {
            background: rgba(105, 108, 255, 0.1);
            color: var(--primary-color);
            font-weight: 600;
            border-left-color: var(--primary-color);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        .nav-badge {
            background: var(--danger-color);
            color: white;
            font-size: 0.625rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            margin-left: auto;
        }

        .nav-divider {
            height: 1px;
            background: var(--border-color);
            margin: 1rem 1.25rem;
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            background: var(--light-bg);
            min-height: 100vh;
        }

        .main-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--light-bg);
            font-size: 0.875rem;
            width: 300px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            background: var(--light-bg);
        }

        .notification-badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            background: var(--danger-color);
            color: white;
            font-size: 0.625rem;
            font-weight: 600;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: var(--light-bg);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .user-company {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
        }

        .content-area {
            padding: 1.5rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
        }

        .page-subtitle {
            color: var(--text-muted);
            margin: 0;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.15);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.25rem 0.5rem rgba(165, 163, 174, 0.15);
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: var(--light-bg);
            border: none;
            color: var(--text-primary);
            font-weight: 600;
            padding: 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background: rgba(105, 108, 255, 0.05);
        }

        .table tbody td {
            padding: 1rem;
            border: none;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table tbody tr.expanded {
            background: rgba(105, 108, 255, 0.05);
        }

        .expand-btn {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 0.875rem;
            padding: 0.25rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .expand-btn:hover {
            background: rgba(105, 108, 255, 0.1);
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.875rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(165, 163, 174, 0.15);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-warning {
            background: var(--warning-color);
            color: white;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.15);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 0.25rem 0.5rem rgba(165, 163, 174, 0.15);
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin: 0;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .stat-icon.primary { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); }
        .stat-icon.success { background: linear-gradient(135deg, var(--success-color), #5cb85c); }
        .stat-icon.warning { background: linear-gradient(135deg, var(--warning-color), #f0ad4e); }
        .stat-icon.danger { background: linear-gradient(135deg, var(--danger-color), #d9534f); }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
        }

        .stat-change {
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-change.positive { color: var(--success-color); }
        .stat-change.negative { color: var(--danger-color); }

        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(113, 221, 55, 0.1);
            color: #2d5016;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: rgba(255, 62, 29, 0.1);
            color: #8b1e13;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background: rgba(255, 171, 0, 0.1);
            color: #8b6914;
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background: rgba(3, 195, 236, 0.1);
            color: #0c5460;
            border-left: 4px solid var(--info-color);
        }

        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background: var(--card-bg);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
            outline: none;
        }

        .pre-scrollable {
            background: #1a1b23;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .font-monospace {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        }

        .text-nowrap {
            white-space: nowrap;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background: rgba(0, 0, 0, 0.02);
        }

        .table-hover tbody tr:hover {
            background: rgba(105, 108, 255, 0.05);
        }

        .copy-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 0.875rem;
            padding: 0.25rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: rgba(105, 108, 255, 0.1);
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                    <div class="sidebar-logo-icon">D</div>
                    <div>
                        <div class="sidebar-logo-text">DevOps</div>
                        <div class="sidebar-logo-subtitle">Admin Panel</div>
                    </div>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-house"></i>
                            Home
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}" href="{{ route('admin.logs') }}">
                            <i class="bi bi-file-text"></i>
                            Logs
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.jobs*') ? 'active' : '' }}" href="{{ route('admin.jobs') }}">
                            <i class="bi bi-briefcase"></i>
                            Jobs
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Deployment</div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.deploy*') ? 'active' : '' }}" href="{{ route('admin.deploy') }}">
                            <i class="bi bi-rocket-takeoff"></i>
                            Deploy
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.cache*') ? 'active' : '' }}" href="{{ route('admin.cache') }}">
                            <i class="bi bi-database"></i>
                            Cache
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.system*') ? 'active' : '' }}" href="{{ route('admin.system') }}">
                            <i class="bi bi-cpu"></i>
                            System
                        </a>
                    </div>
                </div>

                <div class="nav-divider"></div>
                
                <div class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-question-circle"></i>
                        Help
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-box-arrow-right"></i>
                        Log out
                    </a>
                </div>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="main-header">
                <div class="header-left">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                </div>
                <div class="header-right">
                    <button class="notification-btn">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="user-profile">
                        <div class="user-avatar">A</div>
                        <div class="user-info">
                            <div class="user-name">Admin User</div>
                            <div class="user-company">DevOps Team</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>