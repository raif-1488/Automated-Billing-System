<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-...hash..." crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
/* Sidebar container */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 160px;
    background: #ffffff;
    color: #333;
    font-family: Arial, sans-serif;
    padding: 15px 10px;
    transition: width 0.3s ease;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);  /* smoother separation */
    border-right: 1px solid #eee;
    z-index: 1000;
    overflow: hidden;
}

/* Collapsed sidebar */
.sidebar.collapsed {
    width: 60px;
}

/* Sidebar logo */
.sidebar .logo {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
}

.sidebar img {
    width: 100px;
    max-height: 45px;
    object-fit: contain;
    transition: all 0.3s ease;
}

.sidebar.collapsed img {
    width: 35px;
    opacity: 0.9;
}

/* Sidebar nav */
.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar li {
    margin: 10px 0;
}

.sidebar .nav-link {
    color: #555 !important;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 8px;
    transition: background 0.2s ease, color 0.2s ease;
    font-size: 15px;
}

.sidebar .nav-link i {
    min-width: 25px;
    text-align: center;
}

/* Hover and Active */
.sidebar .nav-link:hover {
    background: #f5f5f5;
    color: #222 !important;
}

.sidebar .nav-link.active {
    background-color: #F4C430;
    color: #fff !important;
    font-weight: 600;
}

/* Collapsed: hide labels, keep icons */
.sidebar.collapsed .label {
    display: none;
}

/* Toggle button */
.toggle-btn {
    background: none;
    border: none;
    color: #333;
    cursor: pointer;
    margin-bottom: 15px;
    font-size: 20px;
}

body.sidebar-collapsed .sidebar {
    width: 60px;
}

</style>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <img src="/automate-billing/images/logo.jpg" alt="Logo">  
    </div>

    <ul class="nav flex-column small">
        <li><a class="nav-link <?php if (str_contains($currentPage, 'dashboard')) echo 'active'; ?>" href="admin-dashboard.php"><i class="fas fa-home"></i> <span class="label">Dashboard</span></a></li>
        <li><a class="nav-link <?php if (str_contains($currentPage, 'consumer')) echo 'active'; ?>" href="consumers.php"><i class="fas fa-users"></i> <span class="label">Consumers</span></a></li>
        <li><a class="nav-link <?php if (str_contains($currentPage, 'service')) echo 'active'; ?>" href="services.php"><i class="fas fa-cogs"></i> <span class="label">Services</span></a></li>
        <li><a class="nav-link <?php if (str_contains($currentPage, 'order')) echo 'active'; ?>" href="order.php"><i class="fas fa-shopping-cart"></i> <span class="label">Orders</span></a></li>
        <li><a class="nav-link <?php if (str_contains($currentPage, 'record')) echo 'active'; ?>" href="record.php"><i class="fas fa-money-bill-wave"></i> <span class="label">Transactions</span></a></li>
    </ul>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
}
</script>
