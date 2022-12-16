<?php

// Include config file
require_once 'config.php';
// Initialize the session
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: index.php");
  exit;
}

 
// Define variables and initialize with empty values
$recepie_name = $recepie_desc = $recepie_type = "";
$name_err = $address_err = $salary_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["certificate_id"]) && !empty($_POST["certificate_id"])){
    // Get hidden input value
    $id = $_POST["certificate_id"];
    
    // Validate name
    $input_name = trim($_POST["certificate_name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var(trim($_POST["certificate_name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $name_err = 'Please enter a valid certificate name.';
    } else{
        $name = $input_name;
    }
    
    // Validate address address
    $input_address = trim($_POST["certificate_desc"]);
    if(empty($input_address)){
        $address_err = 'Please enter an certificate desc.';     
    } else{
        $address = $input_address;
    }
    
    // Validate salary
    $input_salary = trim($_POST["certificate_type"]);
    if(empty($input_salary)){
        $salary_err = "Please enter the certificate type .";     
    } elseif(!ctype_digit($input_salary)){
        $salary_err = 'Please enter a positive certificate type value.';
    } else{
        $salary = $input_salary;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($address_err) && empty($salary_err)){
        // Prepare an update statement
        $sql = "UPDATE certificate SET certificate_name=?, certificate_desc=?, certificate_type=? WHERE certificate_id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_name, $param_address, $param_salary, $param_id);
            
            // Set parameters
            $param_name = $certificate_name;
            $param_address = $certificate_desc;
            $param_salary = $certificate_type;
            $param_id = $certificate_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: allcertificate.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["certificate_id"]) && !empty(trim($_GET["certificate_id"]))){
        // Get URL parameter
        $certificate_id =  trim($_GET["certificate_id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM certificate WHERE certificate_id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $certificate_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $certificate_name = $row["certificate_name"];
                    $certificate_desc = $row["certificate_desc"];
                    $certificate_type = $row["certificate_type"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
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
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
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

    <title> Edit Certificate</title>

    <!-- Bootstrap core CSS -->
    <link href="http://localhost/certificatemanagementsystem/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="http://localhost/certificatemanagementsystem/css/bootstrap.min.cssnarrow-jumbotron.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <?php include 'header.php';?>

        <main role="main">

            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>   Edit Certificate </h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Certificate Name</label>
                            <input type="text" name="certificate_name" class="form-control" value="<?php echo $certificate_name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
                            <label>Certificate Description</label>
                            <textarea name="certificate_desc" class="form-control"><?php echo $certificate_desc; ?></textarea>
                            <span class="help-block"><?php echo $address_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($salary_err)) ? 'has-error' : ''; ?>">
                            <label>Certificate Type (1 - Co-Curricular 2 - Extra-Curricular)</label>
                            <input type="text" name="certificate_type" class="form-control" value="<?php echo $certificate_type; ?>">
                            <span class="help-block"><?php echo $salary_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $certificate_id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="allcertificate.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div> 

        </main>


        <?php include 'footer.php';?>


    </div> <!-- /container -->
</body>
</html>