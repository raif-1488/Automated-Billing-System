<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-...hash..." crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
.sidebar {
    position: fixed;
    height: 100vh;
    width: 170px;
    color: #ccc;
    font-family: Arial, sans-serif;
    padding-top: 15px;
    transition: left 0.3s ease;
    overflow: visible;   /*to make edges appear rounded even when collapsed*/
    border-right: 1px solid #ddd;
}


.sidebar i {
    margin-right: 10px;
}


.sidebar.collapsed {
    width: 60px;
}


.sidebar .logo {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    
}


.sidebar img {
    width: 90px;
    height: 40px;
    margin-right: 20px;
    /* transition: opacity 0.3s ease, transform 0.3s ease; */
}


.sidebar ul {
    list-style: none;
    padding: 5px;
    margin: 0px;
}

.sidebar li {
    display: flex;
    /* gap: 0px; */
    padding: 10px 0px;
    font-size: 16px;
    color: #aaa;
   
}

.sidebar .nav-link {
    color: #aaa !important;
    text-decoration: none !important;
    display: block;     /*previously it was flex i.e.was wrapped around container*/ 
    padding: 10px 8px; /*12px 15px*/
    width: 90%;         /*Ensures full width*/
    border-radius: 8px;
    /* transition: left 0.3s ease; */
    
}

.sidebar.collapsed .nav-link {
    padding: 8px ;
    justify-content: center;
}

.sidebar .nav-link:hover {
    background-color: #e0e0e0;
    color: rgb(249, 249, 249) !important;
}

.sidebar .nav-link.active {
      background-color: #F4C430;
      color: white;
      font-weight: 600;
      
    }


.toggle-btn {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer; 
    width: 90%;
    text-align: center;
    display: block;
    margin-right:3px;
    /* transition: left 0.3s ease; */
    
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
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'dashboard') !== false) echo 'active'; ?>" href="admin-dashboard.php" >
            <i class="fas fa-home me-2"></i> <span class="label">Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'consumer') !== false) echo 'active'; ?>" href="consumers.php">
            <i class="fas fa-users me-2"></i> <span class="label">Consumers</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'service') !== false) echo 'active'; ?>" href="services.php">
            <i class="fas fa-cogs me-2"></i> <span class="label">Services</span>
            </a>
        </li>


        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'order') !== false) echo 'active'; ?>" href="order.php">
            <i class="fas fa-shopping-cart me-2"></i> <span class="label">Orders</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link sidebar-link <?php if (str_contains($currentPage, 'record') !== false) echo 'active'; ?>" href="record.php">
            <i class="fas fa-money-bill-wave"></i> <span class="label">Transactions</span>
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
