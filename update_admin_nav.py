import os
import glob
import re

files = glob.glob(r'c:\xampp\htdocs\trip\admin\*.php')

sidebar_template = '''
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
                            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {active_dashboard}">
                                <i class="fas fa-layer-group text-lg"></i>
                                <span class="font-bold text-sm">Dashboard</span>
                            </a>
                            <a href="manage_places.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {active_places}">
                                <i class="fas fa-map-location-dot text-lg"></i>
                                <span class="font-bold text-sm">Packages</span>
                            </a>
                            <a href="manage_booking.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {active_bookings}">
                                <i class="fas fa-calendar-check text-lg"></i>
                                <span class="font-bold text-sm">Bookings</span>
                            </a>
                            <a href="manage_vehicles.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {active_vehicles}">
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
'''

style_inject = '''
    <style>
        .fa-spin-hover:hover { animation: fa-spin 2s infinite linear; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
'''

for f in files:
    filename = os.path.basename(f)
    print(f"Processing {filename}...")
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    if '<aside' not in content:
        continue
        
    # Determine active classes
    active_dashboard = 'bg-purple-600 text-white shadow-lg shadow-purple-900/20' if filename == 'dashboard.php' else 'text-gray-400 hover:text-white hover:bg-white/5'
    active_places = 'bg-purple-600 text-white shadow-lg shadow-purple-900/20' if filename in ['manage_places.php', 'add_place.php', 'edit_place.php'] else 'text-gray-400 hover:text-white hover:bg-white/5'
    active_bookings = 'bg-purple-600 text-white shadow-lg shadow-purple-900/20' if filename == 'manage_booking.php' else 'text-gray-400 hover:text-white hover:bg-white/5'
    active_vehicles = 'bg-purple-600 text-white shadow-lg shadow-purple-900/20' if filename == 'manage_vehicles.php' else 'text-gray-400 hover:text-white hover:bg-white/5'
    
    current_sidebar = sidebar_template.format(
        active_dashboard=active_dashboard,
        active_places=active_places,
        active_bookings=active_bookings,
        active_vehicles=active_vehicles
    )
    
    # Replace the existing aside block
    content = re.sub(r'<aside.*?</aside>', current_sidebar, content, flags=re.DOTALL)
    
    # Inject styles before </head>
    if style_inject not in content:
        content = content.replace('</head>', f'{style_inject}\n</head>')
    
    # Update body class
    content = content.replace('<body class="bg-gray-100">', '<body class="bg-slate-50">')
    
    # Update mobile header
    content = re.sub(r'<div class="md:hidden.*?</div>', 
                    '<div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center fixed w-full z-40 top-0 border-b border-white/5 shadow-lg">\n        <div class="flex items-center gap-2">\n            <i class="fas fa-plane-departure text-purple-500"></i>\n            <span class="font-bold tracking-tight uppercase text-sm">TravelPro Admin</span>\n        </div>\n        <button onclick="document.getElementById(\'admin-sidebar\').classList.toggle(\'-translate-x-full\')" class="focus:outline-none w-10 h-10 flex items-center justify-center bg-white/5 rounded-lg">\n            <i class="fas fa-bars"></i>\n        </button>\n    </div>', 
                    content, flags=re.DOTALL)

    # Fix CSS path (assets -> asset)
    content = content.replace('../assets/css/style.css', '../asset/css/style.css')

    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f"Successfully refined {f}")
