<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Use password_hash in production

    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Register</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
</head>
<body class="bg-slate-50 min-h-screen">
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
                    <div class="flex items-center space-x-4">
                        <a href="login.php" class="px-6 py-2 border-2 border-purple-800/30 text-purple-500 font-bold rounded-lg hover:border-purple-600 hover:bg-purple-600/10 transition-all text-sm backdrop-blur-sm bg-black/10">Login</a>
                        <a href="register.php" class="px-7 py-3 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition-all text-sm">Register</a>
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
                
                <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4">
                    <a href="login.php" class="w-full py-4 border-2 border-purple-800/30 text-purple-600 rounded-2xl font-bold text-center">Log In</a>
                    <a href="register.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold transition shadow-lg text-center">Create Account</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex items-center justify-center w-full min-h-screen pt-20">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Create Account</h2>
        <?php if(isset($error)) echo "<p class='text-red-500 text-center mb-4'>$error</p>"; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <button type="submit" class="w-full py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Register</button>
        </form>
        <p class="text-center mt-4">Already have an account? <a href="login.php" class="text-purple-600">Login</a></p>
    </div>
</body>
</html>