<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

$bookings_sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id";
$bookings_result = $conn->query($bookings_sql);
$bookings_count = $bookings_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav id="navbar" class="bg-gray-900/95 backdrop-blur-md shadow-2xl fixed w-full z-[100] top-0 border-b border-white/5 h-20">
        <div class="max-w-7xl mx-auto px-6 h-full flex justify-between items-center w-full">
            <!-- Branding -->
            <a href="../index.php" class="flex items-center space-x-2 group">
                <i class="fas fa-plane-departure text-2xl text-purple-600 transform group-hover:-translate-y-1 transition-transform"></i>
                <span class="text-2xl font-black text-white tracking-tight">TravelPro</span>
            </a>

            <!-- Right Side Elements -->
            <div class="flex items-center space-x-10">
                <!-- Navigation Links -->
                <div class="hidden lg:flex items-center space-x-10">
                    <a href="../index.php" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Home</a>
                    <a href="../about.php" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">About Us</a>
                    <a href="../index.php#contact" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Contact Us</a>
                    <!-- Gear Icon -->
                    <a href="#" class="text-yellow-400 hover:text-yellow-300 transition-colors">
                        <i class="fas fa-cog text-lg"></i>
                    </a>
                </div>

                <!-- Icons & Buttons -->
                <div class="flex items-center space-x-6">
                    <!-- User Section -->
                    <div class="flex items-center space-x-6">
                        <div class="hidden sm:flex flex-col items-end border-r border-white/10 pr-4 mr-2">
                            <span class="text-[10px] text-white/40 font-bold uppercase tracking-widest leading-none mb-1">Signed in as</span>
                            <span class="text-sm font-bold text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="profile.php" class="text-purple-400 transition text-sm font-black flex items-center border-b-2 border-purple-600 pb-1">
                                <i class="fas fa-user-circle mr-2 text-lg"></i>Profile
                            </a>
                            <a href="my_bookings.php" class="bg-purple-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-purple-700 hover:shadow-lg transition-all shadow-md shadow-purple-900/20">My Bookings</a>
                            <a href="logout.php" class="text-red-400 hover:text-red-500 transition-colors">
                                <i class="fas fa-power-off text-lg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Admin Link -->
                    <a href="../admin/login.php" class="flex items-center space-x-2 text-white/40 hover:text-white/60 transition-colors group">
                        <i class="fas fa-lock text-sm"></i>
                        <span class="text-[13px] font-medium tracking-wide">Admin</span>
                    </a>

                    <!-- Mobile Burger -->
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="lg:hidden w-11 h-11 flex items-center justify-center rounded-xl bg-white/5 text-white border border-white/10 hover:bg-white/10 transition focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-gray-900/95 backdrop-blur-xl border-t border-white/5 shadow-2xl absolute w-full top-full left-0 z-[100] p-8">
            <div class="flex flex-col items-center space-y-8">
                <a href="../index.php" class="text-lg font-bold text-white tracking-widest uppercase">Home</a>
                <a href="../about.php" class="text-lg font-bold text-white tracking-widest uppercase">About Us</a>
                <a href="../index.php#contact" class="text-lg font-bold text-white tracking-widest uppercase">Contact Us</a>
                
                <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4 text-center">
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 mb-2">
                        <p class="text-xs text-white/40 font-bold uppercase tracking-widest mb-1">Authenticated Account</p>
                        <p class="text-lg font-black text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                    </div>
                    <a href="profile.php" class="w-full py-4 border-2 border-white/10 text-white rounded-2xl font-bold">My Profile</a>
                    <a href="my_bookings.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg">My Bookings</a>
                    <a href="logout.php" class="text-red-400 font-bold py-2">Sign Out</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-20 min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b bg-purple-50 rounded-t-2xl">
                <h1 class="text-2xl font-bold text-gray-800 text-center">My Profile</h1>
            </div>
            
            <div class="p-8">
                <div class="flex flex-col items-center mb-8">
                    <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mb-4 border-4 border-white shadow-lg">
                        <i class="fas fa-user text-4xl text-purple-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p class="text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active User</span>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 text-purple-600">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Member Since</p>
                            <p class="font-medium text-gray-800"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 text-purple-600">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Bookings</p>
                            <p class="font-medium text-gray-800"><?php echo $bookings_count; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t">
                    <a href="my_bookings.php" class="block w-full text-center py-3 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium">
                        View Booking History
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
