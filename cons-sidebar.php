<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-...hash..." crossorigin="anonymous" referrerpolicy="no-referrer" />


<style>
.sidebar {
    position: fixed;
    height: 100vh;
    width: 170px;
    background-color: white;
    color: #ccc;
    font-family: Arial, sans-serif;
    padding-top: 12px;
    padding: 10px;
    /* transition: width 0.3s ease; */
    overflow: visible;
    transition: left 0.3s ease;
    border-right: 1px solid #ddd;
  
}

.sidebar i {
    margin-right: 10px;
}


.sidebar.collapsed {
    width: 30px;
}


.sidebar .logo {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    
}

.sidebar img {
    width: 90px;
    height: 40px;
    margin-right: 70px;
    /* margin-left: 2px; */
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0px;
}

.sidebar li {
    display: flex;
    /* gap: 10px; */
    padding: 10px 0px;  /*for spacing between sidebar items*/
    font-size: 16px;
    color: #aaa;
   
}

.sidebar .nav-link {
    color: #aaa !important;
    text-decoration: none !important;
    display: block;
    padding: 10px 8px;
    width: 90%; 
    /* margin-left : 5px; */
    border-radius: 10px; 
   
}

.sidebar.collapsed .nav-link {
    padding: 8px 2px;
    justify-content: center;
}

.sidebar .nav-link:hover {
    background-color: #e0e0e0;
    color:rgb(249, 249, 249) !important;
    
}

.sidebar .nav-link.active {
      background-color: #F4C430;
      color: white;
      font-weight: 600;
      /* margin-left: 0px; */
    }


.toggle-btn {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer; 
    /* padding: 12px 0px; */
    /* font-size: 20px; */
    width: 90%;
    text-align: center;
    display: block;
   
}

.toggle-icon {
    font-size: 20px;
}

.sidebar.collapsed .label {
    display: none;
}

.sidebar.collapsed img {
    /* display: none; */
    opacity: 0;
}



</style>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <span class="toggle-icon">☰</span> 
        </button>
        <img src="/automate-billing/images/brand-logo.jpg" alt="Logo">  
           
    </div>
    

    <ul class="nav flex-column small">
        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'cons-dashboard') !== false) echo 'active'; ?>" href="cons-dashboard.php" >
            <i class="fas fa-home me-2"></i> <span class="label">Dashboard</span>
            </a>
        </li>
      
        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'cons-order') !== false) echo 'active'; ?>" href="cons-order.php">
            <i class="fas fa-shopping-cart me-2"></i> <span class="label">Orders</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'transaction') !== false) echo 'active'; ?>" href="transactions.php">
            <i class="fas fa-money-bill-wave"></i><span class="label">Transactions</span>

            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'renewal') !== false) echo 'active'; ?>" href="renewal.php">
            <i class="fas fa-sync-alt"></i><span class="label">Renewals</span>

            </a>
        </li>
        
    </ul>
   
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const body = document.body;
        sidebar.classList.toggle('collapsed');
        body.classList.toggle('sidebar-collapsed');
    }
</script>
