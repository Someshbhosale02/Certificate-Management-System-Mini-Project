<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
  header("location: index.php");
  exit;
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="http://localhost/CertificateManagementSystem/img/favicon.ico">
  <title> Dashboard | Certificate Management System </title>
  <!-- Bootstrap core CSS -->
  <link href="http://localhost/CertificateManagementSystem/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="http://localhost/CertificateManagementSystem/css/bootstrap.min.cssnarrow-jumbotron.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <?php include 'header.php'; ?>
    <main role="main">
      <div class="alert alert-primary" role="alert">
        Dashboard | Hi, <b>
          <?php echo $_SESSION['username']; ?>
        </b>. Welcome to Certificate Management System site.
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">
                <?php
                if ($_SESSION['is_admin']) {
                  echo ("View All Student Certificates");
                } else {
                  echo ("View My Certificates");
                }
                ?>
              </h4>
              <h6 class="card-subtitle mb-2 text-muted">
                <?php
              if ($_SESSION['is_admin']) {
                echo ("The institution can view and upload the certificates of students. ");
              } else {
                echo ("You can view and upload your certificates.");
              }
              ?>
              </h6>
              <p class="card-text"></p>
              <a href="allcertificate.php" class="card-link">View Certificates</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">
                <?php
              if ($_SESSION['is_admin']) {
                echo ("Adin Profile");
              } else {
                echo ("Student Profile");
              }
              ?>
              </h4>
              <h6 class="card-subtitle mb-2 text-muted">
                <?php
                if ($_SESSION['is_admin']) {
                  echo ("Can Update Admin profile at any time");
                } else {
                  echo ("Can Update Student profile at any time");
                }
                ?>
                </ </h6>
                <p class="card-text"></p>
                <?php 
                if($_SESSION['is_admin']) {
                  echo("<a href=\"admin.php\" class=\"card-link\"> Update Admin Profile </a>");
                } else {
                  echo("<a href=\"student.php\" class=\"card-link\"> Update Student Profile </a>");
                }
                ?>

                
            </div>
          </div>

        </div>

      </div>
    </main>


    <?php include 'footer.php'; ?>


  </div> <!-- /container -->
</body>

</html>