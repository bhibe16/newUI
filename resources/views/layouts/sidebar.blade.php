<div class="w-64 bg-white text-gray-700 font-medium min-h-screen transition-all duration-300 ease-in-out shadow-lg border-r border-gray-100" id="sidebar">
    <!-- Hide/Unhide Button -->
<div class="flex justify-end p-4">
    <button id="toggle-sidebar" class="text-gray-600 hover:text-indigo-600 focus:outline-none transition-colors" aria-label="Toggle sidebar">
        <svg id="toggle-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300 transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <!-- Default: Chevron Left -->
            <path id="icon-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
</div>


    <!-- Sidebar Content -->
    <ul class="space-y-1 pb-4">
        <!-- Dashboard Dropdown -->
        <li id="dashboard-dropdown-parent" class="dropdown-parent">
            <button 
                id="dashboard-dropdown" 
                class="block py-3 px-4 w-full text-left hover:bg-gray-50 rounded-lg mx-2 flex items-center justify-between transition-all duration-200 group"
                onclick="toggleDropdown('dashboard')"
                aria-expanded="false"
                aria-controls="dashboard-submenu"
            >
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-gray-50 group-hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" class="fill-current text-gray-600">
                            <path d="M520-600v-240h320v240H520ZM120-440v-400h320v400H120Zm400 320v-400h320v400H520Zm-400 0v-240h320v240H120Zm80-400h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z"/>
                        </svg>
                    </div>
                    <span class="text-gray-700 group-hover:text-gray-900 transition-colors">Dashboard</span>
                </div>
                <svg 
                    id="dashboard-icon" 
                    class="dropdown-icon w-4 h-4 transition-transform duration-300 transform rotate-0 text-gray-400 group-hover:text-gray-600" 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <ul id="dashboard-submenu" class="submenu space-y-1 pl-14 hidden mt-1 transition-all duration-300 ease-in-out">
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'hr3')
                <li><a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'dashboard')">Admin Dashboard</a></li>
                @elseif (Auth::user()->role == 'Employee')
                <li><a href="{{ route('employee.dashboard') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'dashboard')">Employee dashboard</a></li>
                @endif
            </ul>
        </li>

        <!-- Employees Dropdown -->
        <li id="employees-dropdown-parent" class="dropdown-parent">
            <button 
                id="employees-dropdown" 
                class="block py-3 px-4 w-full text-left hover:bg-gray-50 rounded-lg mx-2 flex items-center justify-between transition-all duration-200 group"
                onclick="toggleDropdown('employees')"
                aria-expanded="false"
                aria-controls="employees-submenu"
            >
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-gray-50 group-hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" viewBox="0 -960 960 960" class="fill-current text-gray-600">
                            <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17-62.5t47-43.5q60-30 124.5-46T480-440q67 0 131.5 16T736-378q30 15 47 43.5t17 62.5v112H160Zm320-400q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm160 228v92h80v-32q0-11-5-20t-15-14q-14-8-29.5-14.5T640-332Zm-240-21v53h160v-53q-20-4-40-5.5t-40-1.5q-20 0-40 1.5t-40 5.5ZM240-240h80v-92q-15 5-30.5 11.5T260-306q-10 5-15 14t-5 20v32Zm400 0H320h320ZM480-640Z"/>
                        </svg>
                    </div>
                    <span class="text-gray-700 group-hover:text-gray-900 transition-colors">Employees</span>
                </div>
                <svg 
                    id="employees-icon" 
                    class="dropdown-icon w-4 h-4 transition-transform duration-300 transform rotate-0 text-gray-400 group-hover:text-gray-600" 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <ul id="employees-submenu" class="submenu space-y-1 pl-14 hidden mt-1 transition-all duration-300 ease-in-out">
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'hr3')
                <li><a href="{{ route('admin.employees.index') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'employees')">Employee Records</a></li>
                <li><a href="{{ route('admin.newhiredemp.index') }}"  class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'employees')">New Hire List</a></li>
                @elseif (Auth::user()->role == 'Employee')
                <li><a href="{{ route('employee.records.index') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'employees')">My records</a></li>
                @endif
            </ul>
        </li>

        <!-- Document Dropdown -->
        <li id="document-dropdown-parent" class="dropdown-parent">
            <button 
                id="document-dropdown" 
                class="block py-3 px-4 w-full text-left hover:bg-gray-50 rounded-lg mx-2 flex items-center justify-between transition-all duration-200 group"
                onclick="toggleDropdown('document')"
                aria-expanded="false"
                aria-controls="document-submenu"
            >
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-gray-50 group-hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" viewBox="0 -960 960 960" class="fill-current text-gray-600">
                            <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
                        </svg>
                    </div>
                    <span class="text-gray-700 group-hover:text-gray-900 transition-colors">Document</span>
                </div>
                <svg 
                    id="document-icon" 
                    class="dropdown-icon w-4 h-4 transition-transform duration-300 transform rotate-0 text-gray-400 group-hover:text-gray-600" 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <ul id="document-submenu" class="submenu space-y-1 pl-14 hidden mt-1 transition-all duration-300 ease-in-out">
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'hr3')
                <li><a href="{{ route('admin.documents.index') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'document')">Submitted Documents</a></li>
                @elseif (Auth::user()->role == 'Employee')
                <li><a href="{{ route('employee.documents.index') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'document')">Documents</a></li>
                @endif
            </ul>
        </li>

        <!-- Jobpost Dropdown -->
        <li id="jobpost-dropdown-parent" class="dropdown-parent">
            <button 
                id="jobpost-dropdown" 
                class="block py-3 px-4 w-full text-left hover:bg-gray-50 rounded-lg mx-2 flex items-center justify-between transition-all duration-200 group"
                onclick="toggleDropdown('jobpost')"
                aria-expanded="false"
                aria-controls="jobpost-submenu"
            >
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-gray-50 group-hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" viewBox="0 0 100 100" class="fill-current text-gray-600">
                            <circle cx="46.3" cy="36.3" r="16"/>
                            <path d="M66.6,51.1A11.39,11.39,0,0,0,55.2,62.5c0,7.7,8.1,15,10.6,16.9a1.09,1.09,0,0,0,1.5,0c2.5-2,10.6-9.2,10.6-16.9A11.25,11.25,0,0,0,66.6,51.1Zm0,16a4.7,4.7,0,1,1,4.7-4.7A4.76,4.76,0,0,1,66.6,67.1Z"/>
                            <path d="M50.4,79.7h1.4c5.2-.5,2.4-3.7,2.4-3.7h0c-3.2-4.6-5-9.1-5-13.5a13.74,13.74,0,0,1,.6-4.2c.2-2-.6-2.5-1-2.7h-.2a18.48,18.48,0,0,0-2.4-.1,24.26,24.26,0,0,0-24,20.9c0,1.2.4,3.5,4.2,3.5H50.2C50.2,79.7,50.3,79.7,50.4,79.7Z"/>
                        </svg>
                    </div>
                    <span class="text-gray-700 group-hover:text-gray-900 transition-colors">Job Post</span>
                </div>
                <svg 
                    id="jobpost-icon" 
                    class="dropdown-icon w-4 h-4 transition-transform duration-300 transform rotate-0 text-gray-400 group-hover:text-gray-600" 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <ul id="jobpost-submenu" class="submenu space-y-1 pl-14 hidden mt-1 transition-all duration-300 ease-in-out">
                <li><a href="{{ route('jobposts.index') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'jobpost')">Job Lists</a></li>
            </ul>
        </li>

        <!-- Performance Dropdown (Only for Admin/HR3) -->
        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'hr3')
        <li id="performance-dropdown-parent" class="dropdown-parent">
            <button 
                id="performance-dropdown" 
                class="block py-3 px-4 w-full text-left hover:bg-gray-50 rounded-lg mx-2 flex items-center justify-between transition-all duration-200 group"
                onclick="toggleDropdown('performance')"
                aria-expanded="false"
                aria-controls="performance-submenu"
            >
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-gray-50 group-hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" viewBox="0 -960 960 960" class="fill-current text-gray-600">
                            <path d="M480-720q-33 0-56.5-23.5T400-800q0-33 23.5-56.5T480-880q33 0 56.5 23.5T560-800q0 33-23.5 56.5T480-720ZM360-80v-520q-60-5-122-15t-118-25l20-80q78 21 166 30.5t174 9.5q86 0 174-9.5T820-720l20 80q-56 15-118 25t-122 15v520h-80v-240h-80v240h-80Z"/>
                        </svg>
                    </div>
                    <span class="text-gray-700 group-hover:text-gray-900 transition-colors">Performance</span>
                </div>
                <svg 
                    id="performance-icon" 
                    class="dropdown-icon w-4 h-4 transition-transform duration-300 transform rotate-0 text-gray-400 group-hover:text-gray-600" 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <ul id="performance-submenu" class="submenu space-y-1 pl-14 hidden mt-1 transition-all duration-300 ease-in-out">
                <li><a href="{{ route('successionplanning.index') }}" class="block py-2 px-4 hover:bg-gray-50 rounded-lg text-gray-600 hover:text-gray-900 transition-all duration-200 text-sm text-center" onclick="handleNavClick(this, 'performance')">Succession Planning</a></li>
            </ul>
        </li>
        @endif

        <!-- Trash Section (Admin Only) 
        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'hr3')
            <li>
                <a 
                    href="{{ route('admin.employees.archived') }}" 
                    class="block py-3 px-4 w-full text-left hover:bg-gray-50 rounded-lg mx-2 flex items-center justify-between transition-all duration-200 group"
                    onclick="handleNavClick(this, 'trash')"
                >
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-lg bg-gray-50 group-hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" viewBox="0 -960 960 960" class="fill-current text-gray-600">
                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                            </svg>
                        </div>
                        <span class="text-gray-700 group-hover:text-gray-900 transition-colors">Trash</span>
                    </div>
                </a>
            </li>
        @endif
        -->
    </ul>
</div>

<style>
    .submenu {
        transition: all 0.3s ease-in-out;
        max-height: 0;
        overflow: hidden;
    }
    
    .submenu:not(.hidden) {
        max-height: 500px; /* Adjust based on your content */
    }
    
    .submenu a {
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .dropdown-icon {
        transition: transform 0.3s ease-in-out;
    }
    
    #sidebar {
        transition: width 0.3s ease-in-out;
    }
</style>

<script>
// Track active menu item
function setActive(element) {
    document.querySelectorAll('.submenu a').forEach(item => {
        item.classList.remove('bg-gray-200', 'text-gray-900');
    });
    element.classList.add('bg-gray-200', 'text-gray-900');
    localStorage.setItem('activeMenuItem', element.getAttribute('href'));
}

// Handle navigation click
function handleNavClick(element, dropdownId) {
    closeDropdown(dropdownId);
    setActive(element);
    return true;
}

// Close specific dropdown
function closeDropdown(dropdownId) {
    const submenu = document.getElementById(`${dropdownId}-submenu`);
    const icon = document.getElementById(`${dropdownId}-icon`);
    const button = document.getElementById(`${dropdownId}-dropdown`);
    
    if (submenu && icon && button) {
        submenu.classList.add('hidden');
        icon.classList.remove('rotate-90');
        button.setAttribute('aria-expanded', 'false');
    }
}

// Restore active menu item on page load
document.addEventListener("DOMContentLoaded", function() {
    const activeMenuItem = localStorage.getItem('activeMenuItem');
    if (activeMenuItem) {
        const item = document.querySelector(`.submenu a[href="${activeMenuItem}"]`);
        if (item) {
            item.classList.add('bg-gray-200', 'text-gray-900');
            const parentId = item.closest('.submenu').id.replace('-submenu', '');
            toggleDropdown(parentId, true);
        }
    }
});

function toggleDropdown(id, forceOpen = false) {
    const submenu = document.getElementById(`${id}-submenu`);
    const icon = document.getElementById(`${id}-icon`);
    const button = document.getElementById(`${id}-dropdown`);
    
    if (forceOpen || submenu.classList.contains('hidden')) {
        document.querySelectorAll('.submenu').forEach(menu => {
            if (menu.id !== `${id}-submenu`) {
                menu.classList.add('hidden');
                const otherIcon = document.getElementById(menu.id.replace('-submenu', '-icon'));
                if (otherIcon) otherIcon.classList.remove('rotate-90');
            }
        });
        
        submenu.classList.remove('hidden');
        icon.classList.add('rotate-90');
        button.setAttribute('aria-expanded', 'true');
    } else {
        submenu.classList.add('hidden');
        icon.classList.remove('rotate-90');
        button.setAttribute('aria-expanded', 'false');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");
    const toggleButton = document.getElementById("toggle-sidebar");
    const toggleIcon = document.getElementById("toggle-icon");
    const dropdownParents = document.querySelectorAll(".dropdown-parent");

    let sidebarState = localStorage.getItem("sidebarState") || "visible";

    function updateSidebar() {
        if (sidebarState === "hidden") {
            sidebar.classList.add("w-20");
            sidebar.classList.remove("w-64");
            sidebar.querySelectorAll("span").forEach(span => span.classList.add("hidden"));
            toggleIcon.classList.add("rotate-180");
            document.querySelectorAll('.submenu').forEach(menu => menu.classList.add('hidden'));
            document.querySelectorAll('.dropdown-icon').forEach(icon => icon.classList.remove('rotate-90'));
        } else {
            sidebar.classList.remove("w-20");
            sidebar.classList.add("w-64");
            sidebar.querySelectorAll("span").forEach(span => span.classList.remove("hidden"));
            toggleIcon.classList.remove("rotate-180");
        }
    }

    toggleButton.addEventListener("click", function() {
        sidebarState = sidebarState === "visible" ? "hidden" : "visible";
        localStorage.setItem("sidebarState", sidebarState);
        updateSidebar();
    });

    dropdownParents.forEach(parent => {
        parent.addEventListener("click", function() {
            if (sidebarState === "hidden") {
                sidebarState = "visible";
                localStorage.setItem("sidebarState", sidebarState);
                updateSidebar();
            }
        });
    });

    updateSidebar();
    
    document.getElementById('toggle-sidebar').addEventListener('click', function () {
    const iconPath = document.getElementById('icon-path');
    const currentPath = iconPath.getAttribute('d');

    // Toggle between left and right chevron
    if (currentPath === "M15 19l-7-7 7-7") {
        iconPath.setAttribute('d', "M9 5l7 7-7 7"); // Chevron right
    } else {
        iconPath.setAttribute('d', "M15 19l-7-7 7-7"); // Chevron left
    }
});
});
</script>