<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TravelPro</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        .about-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1600') center/cover;
            height: 400px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav id="navbar" class="bg-black/20 backdrop-blur-md shadow-2xl fixed w-full z-[100] top-0 border-b border-white/5 h-20">
        <div class="max-w-7xl mx-auto px-6 h-full flex justify-between items-center w-full">
            <!-- Branding -->
            <a href="index.php" class="flex items-center space-x-2 group">
                <i class="fas fa-plane-departure text-2xl text-purple-600 transform group-hover:-translate-y-1 transition-transform"></i>
                <span class="text-2xl font-black text-white tracking-tight">TravelPro</span>
            </a>

            <!-- Right Side Elements -->
            <div class="flex items-center space-x-10">
                <!-- Navigation Links -->
                <div class="hidden lg:flex items-center space-x-10">
                    <a href="index.php" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Home</a>
                    <a href="about.php" class="text-[13px] font-black text-white tracking-widest uppercase border-b-2 border-purple-600 pb-1">About Us</a>
                    <a href="index.php#contact" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Contact Us</a>
                    <!-- Gear Icon -->
                    <a href="#" class="text-yellow-400 hover:text-yellow-300 transition-colors">
                        <i class="fas fa-cog text-lg"></i>
                    </a>
                </div>

                <!-- Icons & Buttons -->
                <div class="flex items-center space-x-6">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- User Section -->
                        <div class="flex items-center space-x-6">
                            <div class="hidden sm:flex flex-col items-end border-r border-white/10 pr-4 mr-2">
                                <span class="text-[10px] text-white/40 font-bold uppercase tracking-widest leading-none mb-1">Signed in as</span>
                                <span class="text-sm font-bold text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="user/profile.php" class="text-white hover:text-purple-400 transition text-sm font-bold flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-lg text-purple-600"></i>Profile
                                </a>
                                <a href="user/my_bookings.php" class="bg-purple-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-purple-700 hover:shadow-lg transition-all shadow-md shadow-purple-900/20">My Bookings</a>
                                <a href="user/logout.php" class="text-red-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-power-off text-lg"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Auth Actions -->
                        <div class="flex items-center space-x-4">
                            <a href="user/login.php" class="px-6 py-2 border-2 border-purple-800/30 text-purple-500 font-bold rounded-lg hover:border-purple-600 hover:bg-purple-600/10 transition-all text-sm backdrop-blur-sm bg-black/10">Login</a>
                            <a href="user/register.php" class="px-7 py-3 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 hover:shadow-lg transition-all text-sm">Register</a>
                        </div>
                    <?php endif; ?>

                    <!-- Admin Link -->
                    <a href="admin/login.php" class="flex items-center space-x-2 text-white/40 hover:text-white/60 transition-colors group">
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
        <div id="mobile-menu" class="hidden lg:hidden bg-black/95 backdrop-blur-xl border-t border-white/5 shadow-2xl absolute w-full top-full left-0 z-[100] p-8">
            <div class="flex flex-col items-center space-y-8">
                <a href="index.php" class="text-lg font-bold text-white tracking-widest uppercase">Home</a>
                <a href="about.php" class="text-lg font-bold text-purple-600 tracking-widest uppercase">About Us</a>
                <a href="index.php#contact" class="text-lg font-bold text-white tracking-widest uppercase">Contact Us</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4 text-center">
                        <div class="bg-white/5 p-6 rounded-2xl border border-white/10 mb-2">
                            <p class="text-xs text-white/40 font-bold uppercase tracking-widest mb-1">Authenticated Account</p>
                            <p class="text-lg font-black text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        </div>
                        <a href="user/profile.php" class="w-full py-4 border-2 border-white/10 text-white rounded-2xl font-bold">My Profile</a>
                        <a href="user/my_bookings.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg">My Bookings</a>
                        <a href="user/logout.php" class="text-red-400 font-bold py-2">Sign Out</a>
                    </div>
                <?php else: ?>
                    <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4">
                        <a href="user/login.php" class="w-full py-4 border-2 border-purple-800/30 text-purple-600 rounded-2xl font-bold text-center">Log In</a>
                        <a href="user/register.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold transition shadow-lg text-center">Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div>
        <header class="about-header flex items-center justify-center text-white pt-20">
            <h1 class="text-5xl font-black tracking-tight uppercase">About Us</h1>
        </header>

        <div class="max-w-7xl mx-auto px-4 py-16">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6 text-gray-800">We Make Your Travel Dreams Reality</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Founded in 2026, TravelPro has quickly grown to become a premier tourism management service. 
                        Our mission is simple: to connect travelers with the most beautiful, exciting, and serene destinations around the globe.
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Whether you're seeking a solo adventure in the mountains, a romantic getaway on a beach, or a fun-filled family vacation, 
                        our curated packages are designed to provide hassle-free and unforgettable experiences.
                    </p>
                    <div class="grid grid-cols-2 gap-6 mt-8">
                        <div>
                            <h4 class="text-4xl font-bold text-purple-600">500+</h4>
                            <p class="text-gray-500">Destinations</p>
                        </div>
                        <div>
                            <h4 class="text-4xl font-bold text-purple-600">10k+</h4>
                            <p class="text-gray-500">Happy Travelers</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800" class="rounded-lg shadow-2xl w-full" alt="About Travel">
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-lg shadow-xl hidden md:block">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600">
                                <i class="fas fa-medal text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Certified Agency</p>
                                <p class="font-bold text-gray-800">Best in Class</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <footer class="bg-gray-800 text-white py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 TravelPro. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
