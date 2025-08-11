<style>
.navbar {
    margin-left: 170px;
    margin-right: 0px;
    /* width: calc(100% - 220px); */
    height: 60px;
    
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 25px;
    /* box-shadow: 0 1px 5px rgba(0,0,0,0.1); */
    font-family: Arial, sans-serif;
    transition: margin-left 0.3s ease;
    border-bottom: 1px solid #ddd;
}

body.sidebar-collapsed .navbar {
    margin-left: 60px; 
}


.nav-left {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 20px; 
    position: relative;
}

.profile-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.admin-dropdown {
    cursor: pointer;
    position: relative;
    font-size: 14px;
    color: #333;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 40px;
    right: 0;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 4px;
    overflow: hidden;
    z-index: 1000;
}

.dropdown-menu a {
    display: block;
    padding: 10px 15px;
    font-size: 14px;
    color: #333;
    text-decoration: none;
}

.dropdown-menu a:hover {
    background-color: #f0f0f0;
}
</style>


<div class="navbar">
    
    <div></div>

    <div class="nav-right">
        <img src="/automate-billing/images/admin-icon.jpg" alt="Profile" class="profile-icon">
        <div class="admin-dropdown" onclick="toggleDropdown()">
            Hello, Admin ▼
            <div class="dropdown-menu" id="adminDropdown">
                <a href="/automate-billing/admin/settings/profile.php">My Profile</a>
                <a href="/automate-billing/admin/settings/account-settings.php">Settings</a>
                <a href="/automate-billing/admin/settings/logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>


<script>
function toggleDropdown() {
    const menu = document.getElementById('adminDropdown');

    // Toggle visibility
    const isOpen = menu.style.display === 'block';
    menu.style.display = isOpen ? 'none' : 'block';

    // Attach one-time click listener to close when clicking outside
    if (!isOpen) {
        document.addEventListener('click', function handler(e) {
            // If the click is outside the dropdown or toggle
            if (!menu.contains(e.target) && !event.target.closest('.admin-dropdown')) {
                menu.style.display = 'none';
                document.removeEventListener('click', handler); // Remove after one use
            }
        });
    }
}
</script>

