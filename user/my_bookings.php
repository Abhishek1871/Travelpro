<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Bookings</title>
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
                            <a href="profile.php" class="text-white hover:text-purple-400 transition text-sm font-bold flex items-center">
                                <i class="fas fa-user-circle mr-2 text-lg text-purple-600"></i>Profile
                            </a>
                            <a href="my_bookings.php" class="bg-purple-600 text-white px-6 py-2.5 rounded-xl text-xs font-black hover:bg-purple-700 hover:shadow-lg transition-all shadow-md shadow-purple-900/20 border-b-2 border-purple-800">My Bookings</a>
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

    <div class="pt-20 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8">My Bookings</h1>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'booked') echo '<div class="bg-green-100 text-green-700 p-4 rounded mb-6">Payment Successful! Booking Confirmed.</div>'; ?>
        
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Package</th>
                        <th class="p-4">Dates</th>
                        <th class="p-4">Total Price</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Booked On</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    <?php
                    $uid = $_SESSION['user_id'];
                    $sql = "SELECT b.*, p.name as place_name, p.image_path FROM bookings b JOIN places p ON b.place_id = p.id WHERE b.user_id = $uid ORDER BY b.created_at DESC";
                    $result = $conn->query($sql);
                    
                    $hasPhpBookings = false;
                    if ($result && $result->num_rows > 0) {
                        $hasPhpBookings = true;
                        while($row = $result->fetch_assoc()) {
                            $statusClass = '';
                            if($row['status'] == 'Confirmed') $statusClass = 'text-green-600 bg-green-100';
                            elseif($row['status'] == 'Cancelled') $statusClass = 'text-red-600 bg-red-100';
                            else $statusClass = 'text-yellow-600 bg-yellow-100';
                            
                            echo '<tr class="border-b hover:bg-gray-50 php-booking-row">';
                            echo '<td class="p-4 flex items-center gap-3">';
                            echo '<img src="../' . $row['image_path'] . '" class="w-16 h-12 object-cover rounded">';
                            echo '<span class="font-medium">' . htmlspecialchars($row['place_name']) . '</span>';
                            echo '</td>';
                            echo '<td class="p-4 text-gray-600">' . $row['from_date'] . ' to ' . $row['to_date'] . '</td>';
                            echo '<td class="p-4 text-purple-600 font-bold">₹' . number_format($row['total_price']) . '</td>';
                            echo '<td class="p-4"><span class="px-3 py-1 rounded-full text-sm font-semibold ' . $statusClass . '">' . $row['status'] . '</span></td>';
                            echo '<td class="p-4 text-gray-500 text-sm">' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                            echo '<td class="p-4">';
                            echo '<a href="ticket.php?id=' . $row['id'] . '" target="_blank" class="text-purple-600 hover:text-purple-800 font-semibold text-sm flex items-center"><i class="fas fa-file-invoice mr-1"></i> Receipt</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr id="phpNoBookings"><td colspan="6" class="p-8 text-center text-gray-500">Loading bookings...</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JS implementation to merge simulated localStorage bookings from index.html -> my_bookings.php -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.getElementById('bookingsTableBody');
            const noBookingsRow = document.getElementById('phpNoBookings');
            const phpUserName = "<?php echo addslashes($_SESSION['user_name']); ?>";
            
            const bookings = JSON.parse(localStorage.getItem('bookings') || '[]');
            const user = JSON.parse(localStorage.getItem('currentUser'));
            
            let userBookings = [];
            if (user) {
                userBookings = bookings.filter(b => b.userId === user.id).reverse();
            } else {
                userBookings = bookings.filter(b => b.userName === phpUserName).reverse();
            }
            
            const hasPhpBookings = <?php echo $hasPhpBookings ? 'true' : 'false'; ?>;
            
            if (userBookings.length > 0) {
                if (noBookingsRow) noBookingsRow.remove();
                
                const packages = JSON.parse(localStorage.getItem('packages') || '[]');
                
                let html = userBookings.map(b => {
                    const statusClass = b.status === 'Confirmed' ? 'text-green-600 bg-green-100' : 
                                      (b.status === 'Cancelled' ? 'text-red-600 bg-red-100' : 'text-yellow-600 bg-yellow-100');
                    
                    const pkg = packages.find(p => p.id == b.packageId);
                    const imageUrl = pkg ? pkg.image : 'https://via.placeholder.com/64x48?text=No+Image';
                    const price = b.totalPrice ? '₹' + b.totalPrice.toLocaleString() : '-';
                    
                    const d = new Date(b.createdAt);
                    const dateStr = !isNaN(d) ? d.toLocaleDateString('en-IN', { month: 'short', day: '2-digit', year: 'numeric' }) : '-';
                    
                    return `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 flex items-center gap-3">
                            <img src="${imageUrl}" class="w-16 h-12 object-cover rounded" onerror="this.src='https://via.placeholder.com/64x48'">
                            <span class="font-medium">${b.packageName}</span>
                        </td>
                        <td class="p-4 text-gray-600">${b.dateFrom} to ${b.dateTo}</td>
                        <td class="p-4 text-purple-600 font-bold">${price}</td>
                        <td class="p-4"><span class="px-3 py-1 rounded-full text-sm font-semibold ${statusClass}">${b.status}</span></td>
                        <td class="p-4 text-gray-500 text-sm">${dateStr}</td>
                        <td class="p-4">
                            <button onclick="alert('Digital receipt functionally located in index.html for simulated bookings')" class="text-purple-600 hover:text-purple-800 font-semibold text-sm flex items-center"><i class="fas fa-file-invoice mr-1"></i> Receipt</button>
                        </td>
                    </tr>
                    `;
                }).join('');
                
                tableBody.insertAdjacentHTML('beforeend', html);
            } else if (!hasPhpBookings) {
                if (noBookingsRow) {
                    noBookingsRow.innerHTML = '<td colspan="6" class="p-8 text-center text-gray-500">No bookings found.</td>';
                }
            }
        });
    </script>
</body>
</html>
