
<header class="header clearfix">
  <nav>
    <ul class="nav nav-pills float-right">
      <?php
// If session variable is not set it will redirect to login page
      if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='adminlogin.php'> ADMIN LOGIN </a>";
        echo "</li>";
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='studentlogin.php'> STUDENT LOGIN</a>";
        echo "</li>";    
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='search.php'> SEARCH </a>";
        echo "</li>";
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='about.php'> ABOUT US </a>";
        echo "</li>";
      }
      elseif (isset($_SESSION['username'])) {
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='dashboard.php'> DASHBOARD </a>";
        echo "</li>";
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='logout.php'> LOGOUT </a>";
        echo "</li>";
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='search.php'> SEARCH </a>";
        echo "</li>";
        echo "<li class='nav-item'>";
        echo " <a class='nav-link' href='about.php'> ABOUT US </a>";
        echo "</li>";
      }
      ?>
    </ul>
  </nav>
  <h3 class="text-muted"><?php
// If session variable is not set it will redirect to login page
  if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
    echo " <a  href='index.php'>  Certificate Management System</a>";    
  }
  else{
    echo " <a  href='dashboard.php'>  Certificate Management System</a>";
  }
  ?>
</h3>
</header>