<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // In real app use password_verify($password, $user['password'])
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            // Log login
            $ip = $_SERVER['REMOTE_ADDR'];
            $username = $user['name'];
            $uid = $user['id'];
            $stmt = $conn->prepare("INSERT INTO user_logs (user_id, username, user_ip, action) VALUES (?, ?, ?, 'LOGIN')");
            $stmt->bind_param("iss", $uid, $username, $ip);
            $stmt->execute();

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">User Login</h2>
        <?php if(isset($error)) echo "<p class='text-red-500 text-center mb-4'>$error</p>"; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <button type="submit" class="w-full py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Login</button>
        </form>
        <p class="text-center mt-4">Don't have an account? <a href="register.php" class="text-purple-600">Register</a></p>
        <p class="text-center mt-2"><a href="../index.php" class="text-gray-500">Back to Home</a></p>
    </div>
</body>
</html>