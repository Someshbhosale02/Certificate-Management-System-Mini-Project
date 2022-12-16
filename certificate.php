<?php


// Initialize the session
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: index.php");
  exit;
}
 
 
// Check existence of id parameter before processing further
if(isset($_GET["certificate_id"]) && !empty(trim($_GET["certificate_id"]))){
// Include config file
  require_once 'config.php';

// Prepare a select statement
  $sql = "SELECT * FROM certificate WHERE certificate_id = ?";

  if($stmt = mysqli_prepare($link, $sql)){
// Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_id);

// Set parameters
    $param_id = trim($_GET["certificate_id"]);

// Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
      $result = mysqli_stmt_get_result($stmt);

      if(mysqli_num_rows($result) == 1){
        /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

// Retrieve individual field value
        $name = $row["certificate_name"];
        $address = $row["certificate_desc"];
        $salary = $row["certificate_type"];
      } else{
// URL doesn't contain valid id parameter. Redirect to error page
        header("location: error.php");
        exit();
      }

    } else{
      echo "Oops! Something went wrong. Please try again later.";
    }
  }

// Close statement
  mysqli_stmt_close($stmt);

// Close connection
  mysqli_close($link);
} else{
// URL doesn't contain id parameter. Redirect to error page
  header("location: error.php");
  exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="http://localhost/certificatemanagementsystem/img/favicon.ico">

  <title> View Certificate</title>

  <!-- Bootstrap core CSS -->
  <link href="http://localhost/certificatemanagementsystem/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="http://localhost/certificatemanagementsystem/css/bootstrap.min.cssnarrow-jumbotron.css" rel="stylesheet">
</head>

<body>

  <div class="container">
    <?php include 'header.php';?>

    <main role="main">
      <div class="col-md-12">
        <div class="page-header">
          <h1>View Record Certificate</h1>
        </div>
        <div class="form-group">
          <label>Certificate Name</label>
          <p class="form-control-static"><?php echo $row["certificate_name"]; ?></p>
        </div>
        <div class="form-group">
          <label>Certificate Description</label>
          <p class="form-control-static"><?php echo $row["certificate_desc"]; ?></p>
        </div>
        <div class="form-group">
          <label>Certificate Type (1 - Co-Curricular 2 - Extra-Curricular)</label>
          <p class="form-control-static"><?php echo $row["certificate_type"]; ?></p>
        </div>
        <p><a href="allcertificate.php" class="btn btn-primary">Back</a></p>
      </div>
    </main>


    <?php include 'footer.php';?>


  </div> <!-- /container -->
</body>
</html>