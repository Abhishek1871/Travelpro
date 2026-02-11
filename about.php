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
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .about-header {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1600') center/cover;
            height: 300px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-plane-departure text-2xl text-purple-600"></i>
                    <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-500 bg-clip-text text-transparent">TravelPro</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-purple-600">Home</a>
                    <a href="index.php#packages" class="text-gray-700 hover:text-purple-600">Packages</a>
                    <a href="about.php" class="text-purple-600 font-medium">About</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="user/profile.php" class="text-gray-700 hover:text-purple-600">Profile</a>
                        <a href="user/logout.php" class="text-red-600 hover:text-red-700">Logout</a>
                    <?php else: ?>
                        <a href="user/login.php" class="text-purple-600 font-medium">Login</a>
                        <a href="user/register.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-16">
        <header class="about-header flex items-center justify-center text-white">
            <h1 class="text-4xl font-bold">About Us</h1>
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
