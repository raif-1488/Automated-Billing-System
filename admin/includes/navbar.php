<style>
.navbar {
    margin-left: 160px; /* match sidebar width */
    height: 60px;
    background: #fff;

    display: flex;
    justify-content: flex-end; /* left + right separation */
    align-items: center;
    padding: 0 25px;

    font-family: Arial, sans-serif;
    transition: margin-left 0.3s ease;

    margin-left: 160px;

    /* Softer separation */
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* Collapsed sidebar adjustment */
body.sidebar-collapsed .navbar {
    margin-left: 60px;
}

/* Left part (Page title) */
.nav-left {
    font-size: 16px;
    font-weight: 600;
    color: #444;
}

/* Right section */
.nav-right {
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
}

/* Profile image */
.profile-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #eee;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.profile-icon:hover {
    transform: scale(1.05);
}

/* Dropdown toggle */
.admin-dropdown {
    cursor: pointer;
    font-size: 14px;
    color: #333;
    font-weight: 500;
    position: relative;
    padding: 8px 12px;
    border-radius: 6px;
    transition: background 0.2s ease;
}

.admin-dropdown:hover {
    background: #f9f9f9;
}

/* Dropdown menu */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 45px;
    right: 0;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    border-radius: 8px;
    overflow: hidden;
    min-width: 160px;
    z-index: 1000;
    animation: fadeIn 0.2s ease;
}

.dropdown-menu a {
    display: block;
    padding: 10px 15px;
    font-size: 14px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s ease;
}

.dropdown-menu a:hover {
    background-color: #f5f5f5;
}

/* Smooth dropdown animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>


<div class="navbar">
    <div class="nav-left"></div>
    <div class="nav-right">
        <img src="/automate-billing/images/admin-icon.jpg" alt="Profile" class="profile-icon">
        <div class="admin-dropdown" onclick="toggleDropdown(event)">
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
function toggleDropdown(e) {
    const menu = document.getElementById('adminDropdown');
    const isOpen = menu.style.display === 'block';
    menu.style.display = isOpen ? 'none' : 'block';

    if (!isOpen) {
        document.addEventListener('click', function handler(ev) {
            if (!menu.contains(ev.target) && !ev.target.closest('.admin-dropdown')) {
                menu.style.display = 'none';
                document.removeEventListener('click', handler);
            }
        });
    }
}
</script>
