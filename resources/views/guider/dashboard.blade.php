<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guider Dashboard - WanLanka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a3a8f;
            --primary-light: #4a90e2;
            --secondary: #f8f9fa;
            --accent: #00b894;
            --text: #333;
            --text-light: #6c757d;
            --border: #e0e6ef;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --radius: 12px;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f5f7fb;
            color: var(--text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            z-index: 900;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }
        
        .user-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .user-details p {
            margin: 0;
            font-size: 12px;
            color: var(--text-light);
        }
        
        .sidebar-menu {
            padding: 20px 0;
            flex: 1;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            position: relative;
        }
        
        .menu-item:hover, .menu-item.active {
            background: #f0f7ff;
            color: var(--primary);
            border-left-color: var(--primary);
        }
        
        .menu-item i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        .menu-label {
            font-weight: 500;
        }
        
        .menu-badge {
            margin-left: auto;
            background: var(--primary);
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 12px;
        }
        
        /* Dropdown Styles */
        .menu-item.has-dropdown {
            justify-content: space-between;
        }
        
        .dropdown-icon {
            margin-left: auto;
            font-size: 12px;
            transition: transform 0.3s;
        }
        
        .menu-item.has-dropdown.active .dropdown-icon {
            transform: rotate(180deg);
        }
        
        .submenu {
            display: none;
            background: #f8f9fa;
            border-left: 3px solid var(--primary);
        }
        
        .menu-item.has-dropdown.active + .submenu {
            display: block;
        }
        
        .submenu-item {
            display: flex;
            align-items: center;
            padding: 10px 25px 10px 40px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .submenu-item:hover {
            background: #e9f0ff;
            color: var(--primary);
        }
        
        .submenu-item i {
            margin-right: 10px;
            font-size: 14px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border);
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px;
            background: #ffe6e6;
            color: #d63031;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #ffcccc;
        }
        
        .logout-btn i {
            margin-right: 8px;
        }
        
        /* Logout Confirmation Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .logout-modal {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: translateY(-20px);
            transition: transform 0.3s;
        }
        
        .modal-overlay.active .logout-modal {
            transform: translateY(0);
        }
        
        .modal-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #ffe6e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #d63031;
            font-size: 24px;
        }
        
        .modal-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text);
        }
        
        .modal-text {
            color: var(--text-light);
            margin-bottom: 25px;
        }
        
        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .modal-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .modal-btn.confirm {
            background: #d63031;
            color: white;
        }
        
        .modal-btn.confirm:hover {
            background: #c2362e;
        }
        
        .modal-btn.cancel {
            background: #f8f9fa;
            color: var(--text);
            border: 1px solid var(--border);
        }
        
        .modal-btn.cancel:hover {
            background: #e9ecef;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        /* Dashboard Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--text-light);
            font-size: 14px;
        }
        
        /* Activity Section */
        .activity-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Activity List */
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .activity-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f0f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .activity-time {
            font-size: 12px;
            color: var(--text-light);
        }
        
        /* Upcoming Tours */
        .tour-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .tour-header {
            padding: 15px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
        }
        
        .tour-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }
        
        .tour-body {
            padding: 20px;
        }
        
        .tour-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .tour-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .tour-info i {
            margin-right: 8px;
            color: var(--primary);
        }
        
        .tour-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
        }
        
        /* Custom Utilities */
        .bg-primary-light {
            background: #f0f7ff;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .badge-success {
            background: var(--accent);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
        }
        
        .btn-outline-primary {
            border-color: var(--primary-light);
            color: var(--primary-light);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-light);
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1000;
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 6px;
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 20px 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-actions {
                margin-top: 15px;
            }
            
            .modal-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-text">WanLanka</div>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <?php
                        $gName = 'Guider';
                        $gEmail = null;
                        try {
                            $gId = session('guider_id');
                            if ($gId) {
                                $g = \App\Models\Guider::find($gId);
                                if ($g) {
                                    $gName = $g->username ?? ($g->name ?? 'Guider');
                                    $gEmail = $g->email ?? null;
                                }
                            }
                        } catch (\Throwable $e) { /* view-safe */ }
                    ?>
                    <h4>{{ $gName }}</h4>
                    @if($gEmail)
                        <p>{{ $gEmail }}</p>
                    @else
                        <p>Professional Tour Guide</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <a href="{{ route('guider.dashboard') }}" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span class="menu-label">Dashboard</span>
            </a>
            <a href="#" class="menu-item has-dropdown">
                <i class="fas fa-calendar-alt"></i>
                <span class="menu-label">My Tours</span>
                <i class="fas fa-chevron-down dropdown-icon"></i>
            </a>
            <div class="submenu">
                <?php
                    // Count upcoming individual bookings
                    $individualCount = 0;
                    $fixedCount = 0;
                    $customCount = 0;
                    $gId = session('guider_id');
                    $today = \Carbon\Carbon::today()->toDateString();
                    
                    if ($gId) {
                        try {
                            // Individual bookings count
                            $individualCount = \App\Models\Booking::where('guider_id', $gId)
                                ->whereDate('date', '>=', $today)
                                ->whereNotIn('status', ['cancelled', 'completed'])
                                ->count();
                                
                            // Fixed bookings count (if they have a guider field in the future)
                            // For now, we'll assume fixed bookings don't have guiders assigned directly
                            $fixedCount = 0;
                            
                            // Custom packages count
                            $customCount = \App\Models\CustomPackage::where('guider_id', $gId)
                                ->whereDate('travel_date', '>=', $today)
                                ->whereIn('status', ['approved', 'active'])
                                ->count();
                        } catch (\Throwable $e) {
                            // Ignore errors
                        }
                    }
                ?>
                <a href="{{ route('guider.individual-bookings') }}" class="submenu-item">
                    <i class="fas fa-user"></i>
                    <span>Individual Bookings</span>
                    <?php if($individualCount > 0): ?>
                    <span class="menu-badge"><?php echo e($individualCount); ?></span>
                    <?php endif; ?>
                </a>
                <a href="{{ route('guider.fixed-bookings') }}" class="submenu-item">
                    <i class="fas fa-box"></i>
                    <span>Fixed Bookings</span>
                    <?php if($fixedCount > 0): ?>
                    <span class="menu-badge"><?php echo e($fixedCount); ?></span>
                    <?php endif; ?>
                </a>
                <a href="{{ route('guider.custom-bookings') }}" class="submenu-item">
                    <i class="fas fa-cube"></i>
                    <span>Custom Bookings</span>
                    <?php if($customCount > 0): ?>
                    <span class="menu-badge"><?php echo e($customCount); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <a href="{{ route('guider.messages') }}" class="menu-item">
                <i class="fas fa-comment-dots"></i>
                <span class="menu-label">Messages</span>
                <span class="menu-badge">3</span>
            </a>
            <a href="{{ route('guider.reviews') }}" class="menu-item">
                <i class="fas fa-star"></i>
                <span class="menu-label">Reviews</span>
            </a>
            <a href="{{ route('guider.earnings') }}" class="menu-item">
                <i class="fas fa-chart-line"></i>
                <span class="menu-label">Earnings</span>
            </a>
            <a href="{{ route('guider.settings') }}" class="menu-item">
                <i class="fas fa-cog"></i>
                <span class="menu-label">Settings</span>
            </a>
            <a href="{{ route('guider.help') }}" class="menu-item">
                <i class="fas fa-question-circle"></i>
                <span class="menu-label">Help & Support</span>
            </a>
        </div>
        
        <div class="sidebar-footer">
            <button class="logout-btn" id="logoutTrigger">
                <i class="fas fa-sign-out-alt"></i>Logout
            </button>
            <form id="guider-logout-form" action="{{ route('guider.logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>
    
    <!-- Mobile Menu Toggle -->
    <button class="menu-toggle d-lg-none">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Tour
                </button>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-primary-light text-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-label">Upcoming Tours</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-primary-light text-primary">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-value">47</div>
                <div class="stat-label">Tourists This Month</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-primary-light text-primary">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-value">4.8</div>
                <div class="stat-label">Average Rating</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-primary-light text-primary">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-value">$2,450</div>
                <div class="stat-label">Earnings This Month</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Recent Activity -->
                <div class="activity-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                        <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <ul class="activity-list">
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">New tour booking received</div>
                                    <div class="activity-desc">Colombo City Tour for 4 people on June 15</div>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">New review received</div>
                                    <div class="activity-desc">"Excellent guide with deep knowledge of history" - Sarah Johnson</div>
                                    <div class="activity-time">5 hours ago</div>
                                </div>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Tour completed successfully</div>
                                    <div class="activity-desc">Sigiriya Rock Fortress Tour with 6 tourists</div>
                                    <div class="activity-time">Yesterday, 4:30 PM</div>
                                </div>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-comment"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">New message from tourist</div>
                                    <div class="activity-desc">"Can we adjust the start time for the Kandy tour?"</div>
                                    <div class="activity-time">June 10, 11:20 AM</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Upcoming Packages (Dynamic) -->
                <?php
                    $today = \Carbon\Carbon::today()->toDateString();
                    $gId = session('guider_id');
                    $gEmail = null;
                    $gUsername = null;
                    if ($gId) {
                        try {
                            $g = \App\Models\Guider::find($gId);
                            if ($g) { 
                                $gEmail = $g->email ?? null; 
                                $gUsername = $g->username ?? null; 
                            }
                        } catch (\Throwable $e) { /* ignore */ }
                    }

                    // Upcoming individual bookings assigned to this guider
                    $upcomingBookings = collect();
                    try {
                        $query = \App\Models\Booking::query()
                            ->whereDate('date', '>=', $today)
                            ->where('guider_id', $gId)
                            ->whereNotIn('status', ['cancelled', 'completed'])
                            ->orderBy('date');
                        
                        $upcomingBookings = $query->limit(5)->get();
                    } catch (\Throwable $e) { 
                        $upcomingBookings = collect(); 
                    }

                    // Upcoming custom packages assigned to this guider
                    $upcomingCustomPackages = collect();
                    $customPackagesNote = null;
                    try {
                        if ($gId) {
                            $upcomingCustomPackages = \App\Models\CustomPackage::query()
                                ->where('guider_id', $gId)
                                ->whereDate('travel_date', '>=', $today)
                                ->whereIn('status', ['approved', 'active'])
                                ->orderBy('travel_date')
                                ->limit(5)
                                ->get();
                        } else {
                            $customPackagesNote = 'Unable to load custom packages. Please log in again.';
                        }
                    } catch (\Throwable $e) { 
                        $upcomingCustomPackages = collect();
                        $customPackagesNote = 'Error loading custom packages.';
                    }
                ?>

                <div class="activity-card">
                    <div class="card-header">
                        <h3 class="card-title">Upcoming Bookings</h3>
                    </div>
                    <div class="card-body">
                        @forelse($upcomingBookings as $bk)
                            <div class="tour-card">
                                <div class="tour-header">
                                    <h4 class="tour-title">Booking #{{ $bk->id }}</h4>
                                </div>
                                <div class="tour-body">
                                    <div class="tour-details">
                                        <span class="badge badge-success">{{ ucfirst($bk->status ?? 'pending') }}</span>
                                        <span>{{ $bk->people_count ?? '-' }} people</span>
                                    </div>
                                    <div class="tour-info">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ \Carbon\Carbon::parse($bk->date)->format('M d, Y') }}</span>
                                    </div>
                                    @if(!empty($bk->time))
                                    <div class="tour-info">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $bk->time }}</span>
                                    </div>
                                    @endif
                                    @if(!empty($bk->pickup_location))
                                    <div class="tour-info">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $bk->pickup_location }}</span>
                                    </div>
                                    @endif
                                    <div class="tour-actions">
                                        <form method="POST" action="{{ route('bookings.complete', $bk->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to mark this tour as completed?')">Complete Tour</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No upcoming bookings.</p>
                        @endforelse
                    </div>
                </div>

                <div class="activity-card">
                    <div class="card-header">
                        <h3 class="card-title">Upcoming Custom Packages</h3>
                    </div>
                    <div class="card-body">
                        @if($customPackagesNote)
                            <div class="alert alert-info py-2">{{ $customPackagesNote }}</div>
                        @endif
                        @forelse($upcomingCustomPackages as $cp)
                            <div class="tour-card">
                                <div class="tour-header">
                                    <h4 class="tour-title">{{ $cp->title }}</h4>
                                </div>
                                <div class="tour-body">
                                    <div class="tour-details">
                                        <span class="badge badge-success">{{ ucfirst($cp->status ?? 'pending') }}</span>
                                        <span>{{ $cp->num_people ?? '-' }} people</span>
                                    </div>
                                    @if(!empty($cp->travel_date))
                                    <div class="tour-info">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ \Carbon\Carbon::parse($cp->travel_date)->format('M d, Y') }}</span>
                                    </div>
                                    @endif
                                    @if(!empty($cp->start_location))
                                    <div class="tour-info">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $cp->start_location }}</span>
                                    </div>
                                    @endif
                                    <div class="tour-actions">
                                        <form method="POST" action="{{ route('custom-packages.complete', $cp->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to mark this tour as completed?')">Complete Tour</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No upcoming custom packages.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logout Confirmation Modal -->
    <div class="modal-overlay" id="logoutModal">
        <div class="logout-modal">
            <div class="modal-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3 class="modal-title">Confirm Logout</h3>
            <p class="modal-text">Are you sure you want to logout? You will be redirected to the home page.</p>
            <div class="modal-actions">
                <button class="modal-btn confirm" id="confirmLogout">Yes, Logout</button>
                <button class="modal-btn cancel" id="cancelLogout">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutTrigger = document.getElementById('logoutTrigger');
            const logoutModal = document.getElementById('logoutModal');
            const confirmLogout = document.getElementById('confirmLogout');
            const cancelLogout = document.getElementById('cancelLogout');
            
            // Toggle sidebar on mobile
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
            
            // Add active class to clicked menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Only prevent default for dropdown menu items
                    if (this.classList.contains('has-dropdown')) {
                        e.preventDefault();
                        this.classList.toggle('active');
                        return;
                    }
                    
                    // For regular links, we don't prevent default behavior
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Add active class to submenu items
            const submenuItems = document.querySelectorAll('.submenu-item');
            submenuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    submenuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Logout functionality
            logoutTrigger.addEventListener('click', function() {
                logoutModal.classList.add('active');
            });
            
            confirmLogout.addEventListener('click', function() {
                const form = document.getElementById('guider-logout-form');
                if (form) form.submit();
            });
            
            cancelLogout.addEventListener('click', function() {
                logoutModal.classList.remove('active');
            });
            
            // Close modal when clicking outside
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    logoutModal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>