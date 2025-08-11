<?php

$date = date("l, j F Y");
$user_name = "User";

if(isset($_SESSION['email'])) {
     $stmt = $pdo->prepare("SELECT name FROM consumers WHERE email = ?");
     $stmt->execute([$_SESSION['email']]);
     $row = $stmt->fetch(PDO::FETCH_ASSOC);  //Fetches the result as an associative array
     if ($row) {
        $user_name = $row['name'];
     }
}
?>

<style> 
        .topbar {

            margin-left: 190px;
            margin-right: 0px;
            /* width: calc(100% - 220px); */
            height: 60px;
            
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px; /*cannot be removed*/

            font-family: Arial, sans-serif;
            transition: left 0.3s ease;
            position: relative;
            border-bottom: 1px solid #ddd;
            /* z-index: 10; */
        }

        /* body.sidebar-collapsed .topbar {
            /* margin-left: -150px;  */
            /* width: calc(100% - 20%); */
            /* margin-left: 0px; 
         }  */ 


        .top-left {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
        }

        .top-right i {
            font-size: 18px;
            cursor: pointer;
        } 

        .user-dropdown img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
           
        }

        .user-dropdown {
           
            display: flex;
            cursor: pointer;
            gap: 10px;
            align-items: center;
 
        }

        .user-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border-radius: 5px;
            overflow: hidden;
            z-index: 1000;
        }

        .user-dropdown-content a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .user-dropdown-content a:hover {
            background: #eee;
        }
        
        body.sidebar-collapsed .topbar{
            margin-left: 50px;
        }

</style>


<div class="topbar">
        <div class="top-left"><?= $date ?></div>

        <div class="top-right">
            <!-- <div class="notification-bell <?= $highlight ?>" id="notificationBell">
                <i class="fas fa-bell"></i>
            </div> -->

            <div class="user-dropdown" onclick="toggleDropdown()">
                <img src="images/user-profile-icon.jpg" alt="Profile">
                <span><?= htmlspecialchars($user_name) ?></span>
                
                <div class="user-dropdown-content" id="userDropdown">
                    <a href="cons-profile.php">My Profile</a>
                    <a href="cons-logout.php">Logout</a>
                </div>
            </div>
        </div>
</div>


<script>
function toggleDropdown() {
    const menu = document.getElementById('userDropdown');

    // Toggle visibility
    const isOpen = menu.style.display === 'block';
    menu.style.display = isOpen ? 'none' : 'block';

    // Attach one-time click listener to close when clicking outside
    if (!isOpen) {
        document.addEventListener('click', function handler(e) {
            // If the click is outside the dropdown or toggle
            if (!menu.contains(e.target) && !event.target.closest('.user-dropdown')) {
                menu.style.display = 'none';
                document.removeEventListener('click', handler); // Remove after one use
            }
        });
    }
}
</script>

   