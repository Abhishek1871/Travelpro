<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$placeCount = $conn->query("SELECT COUNT(*) as count FROM places")->fetch_assoc()['count'];
$logCount = $conn->query("SELECT COUNT(*) as count FROM user_logs")->fetch_assoc()['count'];

// ---- Advanced Analytics ----
// Revenue
$revenue = $conn->query("SELECT COALESCE(SUM(total_price),0) as revenue FROM bookings WHERE status='Confirmed'")->fetch_assoc()['revenue'];

// Bookings by status
$statusRows = $conn->query("SELECT status, COUNT(*) cnt FROM bookings GROUP BY status");
$bookingStatus = ['Pending'=>0,'Confirmed'=>0,'Cancelled'=>0];
while($r = $statusRows->fetch_assoc()) { $bookingStatus[$r['status']] = (int)$r['cnt']; }

// Login vs Logout - last 7 days
$labels = [];
$loginData = [];
$logoutData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($date));

    $stmt = $conn->prepare("SELECT 
        SUM(action='LOGIN') as logins,
        SUM(action='LOGOUT') as logouts
        FROM user_logs
        WHERE DATE(login_time)=?");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $loginData[] = (int)($row['logins'] ?? 0);
    $logoutData[] = (int)($row['logouts'] ?? 0);
}

// Top packages
$topPackagesRes = $conn->query("SELECT p.name, COUNT(*) cnt FROM bookings b JOIN places p ON b.place_id=p.id GROUP BY b.place_id ORDER BY cnt DESC LIMIT 5");
$topPackageLabels = [];
$topPackageCounts = [];
while($r = $topPackagesRes->fetch_assoc()) { $topPackageLabels[]=$r['name']; $topPackageCounts[]=(int)$r['cnt']; }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">

    <style>
        .fa-spin-hover:hover { animation: fa-spin 2s infinite linear; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>

</head>
<body class="bg-slate-50">
    <!-- Mobile Header -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center fixed w-full z-40 top-0 border-b border-white/5 shadow-lg">
        <div class="flex items-center gap-2">
            <i class="fas fa-plane-departure text-purple-500"></i>
            <span class="font-bold tracking-tight uppercase text-sm">TravelPro Admin</span>
        </div>
        <button onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')" class="focus:outline-none w-10 h-10 flex items-center justify-center bg-white/5 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="flex min-h-screen pt-16 md:pt-0">
        
        
        <aside id="admin-sidebar" class="w-64 bg-slate-900 text-white fixed h-full z-50 transform -translate-x-full md:translate-x-0 transition-all duration-300 border-r border-white/5">
            <div class="p-6 border-b border-white/5 flex justify-between items-center bg-slate-950/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center shadow-lg shadow-purple-900/40">
                        <i class="fas fa-plane-departure text-sm"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight">TravelPro <span class="text-purple-500 text-xs align-top bg-purple-500/10 px-2 py-0.5 rounded-full ml-1">ADMIN</span></span>
                </div>
                <button onclick="document.getElementById('admin-sidebar').classList.add('-translate-x-full')" class="md:hidden text-gray-400 hover:text-white focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="flex flex-col h-[calc(100%-80px)] justify-between">
                <nav class="p-4 space-y-8 overflow-y-auto custom-scrollbar">
                    <!-- Main Navigation -->
                    <div>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-4 px-2">Main Menu</p>
                        <div class="space-y-1">
                            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all bg-purple-600 text-white shadow-lg shadow-purple-900/20">
                                <i class="fas fa-layer-group text-lg"></i>
                                <span class="font-bold text-sm">Dashboard</span>
                            </a>
                            <a href="manage_places.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
                                <i class="fas fa-map-location-dot text-lg"></i>
                                <span class="font-bold text-sm">Packages</span>
                            </a>
                            <a href="manage_booking.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
                                <i class="fas fa-calendar-check text-lg"></i>
                                <span class="font-bold text-sm">Bookings</span>
                            </a>
                            <a href="manage_vehicles.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
                                <i class="fas fa-car-side text-lg"></i>
                                <span class="font-bold text-sm">Vehicles</span>
                            </a>
                        </div>
                    </div>

                    <!-- Website Tools -->
                    <div>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-4 px-2">Website View</p>
                        <div class="space-y-1">
                            <a href="../index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fas fa-home text-lg"></i>
                                <span class="font-bold text-sm">Home Page</span>
                            </a>
                            <a href="../about.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fas fa-info-circle text-lg"></i>
                                <span class="font-bold text-sm">About Us</span>
                            </a>
                            <a href="../index.php#contact" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fas fa-envelope text-lg"></i>
                                <span class="font-bold text-sm">Contact Us</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Admin Footer -->
                <div class="p-4 border-t border-white/5 bg-slate-950/30">
                    <div class="flex items-center justify-between mb-4 px-2">
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="fas fa-circle text-[8px] text-green-500 animate-pulse"></i>
                            <span class="text-[10px] font-bold uppercase tracking-widest leading-none">Admin Online</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="#" class="text-gray-500 hover:text-white transition-colors">
                                <i class="fas fa-bell text-sm"></i>
                            </a>
                            <a href="#" class="text-yellow-500 hover:text-yellow-400 transition-colors">
                                <i class="fas fa-cog fa-spin-hover text-sm"></i>
                            </a>
                        </div>
                    </div>
                    <a href="logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-400 hover:bg-red-500/10 transition-all font-bold text-sm">
                        <i class="fas fa-power-off"></i>
                        <span>Sign Out</span>
                    </a>
                </div>
            </div>
        </aside>



        <div class="md:ml-64 flex-1 p-4 md:p-8 w-full max-w-full overflow-hidden">
            <h1 class="text-2xl font-bold mb-8">Dashboard Overview</h1>
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg">Total Packages</h3>
                    <p class="text-3xl font-bold"><?php echo $placeCount; ?></p>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg">Total Users</h3>
                    <p class="text-3xl font-bold"><?php echo $userCount; ?></p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg">Total Log Events</h3>
                    <p class="text-3xl font-bold"><?php echo $logCount; ?></p>
                    <p class="text-xs opacity-90 mt-1">Login + Logout</p>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg">Revenue (Confirmed)</h3>
                    <p class="text-3xl font-bold">₹<?php echo number_format($revenue); ?></p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
                    <h3 class="text-lg font-bold mb-4"><i class="fas fa-chart-bar mr-2 text-purple-600"></i>Logins vs Logouts (Last 7 Days)</h3>
                    <canvas id="loginLogoutChart" height="90"></canvas>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold mb-4"><i class="fas fa-layer-group mr-2 text-purple-600"></i>Bookings Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between"><span class="text-gray-600">Pending</span><span class="font-bold text-yellow-600"><?php echo $bookingStatus['Pending']; ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Confirmed</span><span class="font-bold text-green-600"><?php echo $bookingStatus['Confirmed']; ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Cancelled</span><span class="font-bold text-red-600"><?php echo $bookingStatus['Cancelled']; ?></span></div>
                        <div class="pt-4 border-t">
                            <h4 class="font-semibold text-gray-800 mb-2">Top Packages</h4>
                            <?php if(count($topPackageLabels) === 0): ?>
                                <p class="text-sm text-gray-500">No bookings yet.</p>
                            <?php else: ?>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <?php for($i=0; $i<count($topPackageLabels); $i++): ?>
                                        <li class="flex justify-between"><span class="truncate pr-2"><?php echo htmlspecialchars($topPackageLabels[$i]); ?></span><span class="font-bold"><?php echo $topPackageCounts[$i]; ?></span></li>
                                    <?php endfor; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold mb-4">Recent User Logins</h3>
                <div class="overflow-x-auto w-full max-w-full">
                        <table class="w-full text-left min-w-[600px]">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-3">User</th>
                            <th class="p-3">Time</th>
                            <th class="p-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $logs = $conn->query("SELECT * FROM user_logs ORDER BY login_time DESC LIMIT 10");
                        while($log = $logs->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="p-3"><?php echo htmlspecialchars($log['username']); ?></td>
                            <td class="p-3"><?php echo $log['login_time']; ?></td>
                            <td class="p-3"><?php echo $log['user_ip']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                        </div>
            </div>
        </div>

            <script>
                const labels = <?php echo json_encode($labels); ?>;
                const loginData = <?php echo json_encode($loginData); ?>;
                const logoutData = <?php echo json_encode($logoutData); ?>;

                const ctx = document.getElementById('loginLogoutChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Logins',
                                    data: loginData,
                                    backgroundColor: 'rgba(34, 197, 94, 0.65)',
                                    borderColor: 'rgba(34, 197, 94, 1)',
                                    borderWidth: 1,
                                    borderRadius: 8
                                },
                                {
                                    label: 'Logouts',
                                    data: logoutData,
                                    backgroundColor: 'rgba(239, 68, 68, 0.65)',
                                    borderColor: 'rgba(239, 68, 68, 1)',
                                    borderWidth: 1,
                                    borderRadius: 8
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: { mode: 'index', intersect: false }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            </script>
        </div>
    </div>
</body>
</html>