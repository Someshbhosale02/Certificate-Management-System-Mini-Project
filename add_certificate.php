<?php

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("location: index.php");
    exit;
}

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$certificate_name = $certificate_desc = $certificate_type = $student_id = $file_name = $file_tmp = "";
$certificate_name_err = $certificate_desc_err = $certificate_type_err = $student_id_err = $file_name_err = "";


// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    $input_name = trim($_POST["certificate_name"]);
    if (empty($input_name)) {
        $certificate_name_err = "Please enter a certificate name.";
    } elseif (!filter_var(trim($_POST["certificate_name"]), FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z'-.\s ]+$/")))) {
        $certificate_name_err = 'Please enter a valid name.';
    } else {
        $certificate_name = $input_name;
    }

    // Validate address
    $input_address = trim($_POST["certificate_desc"]);
    if (empty($input_address)) {
        $certificate_desc_err = 'Please enter description.';
    } else {
        $certificate_desc = $input_address;
    }

    // Validate student id
    $input_id = trim($_POST["student_id"]);
    if (empty($input_id)) {
        if (!$_SESSION['is_admin']) {
            $student_id = $_SESSION['user_id'];
        } else {
            $student_id_err = 'Please enter roll no.';
        }
        
    } else {
        
        $student_id = $input_id;
    }

    // Validate salary
    $input_salary = trim($_POST["certificate_type"]);
    if (empty($input_salary)) {
        $certificate_type_err = "Please enter the salary amount.";
    } elseif (!ctype_digit($input_salary)) {
        $certificate_type_err = 'Please enter a positive integer value.';
    } else {
        $certificate_type = $input_salary;
    }

    if (isset($_FILES['pdf_file']['name'])) {
        $input_file_name = trim($_FILES["pdf_file"]["name"]);
        if (empty($input_file_name)) {
            $file_name_err = "Please select pdf file.";
        } else {
            $file_name = $input_file_name;
            $file_tmp = $_FILES['pdf_file']['tmp_name'];
        }
    }
    else {
        $file_name_err = "Please select pdf file.";
    }



    // Check input errors before inserting in database
    if (empty($certificate_name_err) && empty($certificate_desc_err) && empty($certificate_type_err) && empty($student_id_err) && empty($file_name_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO certificate (certificate_name, certificate_desc, certificate_type, student_id, file_name) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_certificate_name, $param_certificate_desc, $param_certificate_type, $param_student_id, $param_file_name);

            // Set parameters
            $param_certificate_name = $certificate_name;
            $param_certificate_desc = $certificate_desc;
            $param_certificate_type = $certificate_type;
            $param_student_id = $student_id;
            $param_file_name = $file_name;


            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt) && move_uploaded_file($file_tmp, "./pdf/". mysqli_insert_id($link) . "-" . $file_name )) {
                

                
                // Records created successfully. Redirect to landing page
                header("location: allcertificate.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            
        // Close statement
        mysqli_stmt_close($stmt);
        }
        else {
            echo "Something went wrong. Please try again later.";
        }

    }

    // Close connection
    mysqli_close($link);
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

    <title> Add Cerificate</title>

    <!-- Bootstrap core CSS -->
    <link href="http://localhost/certificatemanagementsystem/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="http://localhost/certificatemanagementsystem/css/bootstrap.min.cssnarrow-jumbotron.css"
        rel="stylesheet">
</head>

<body>

    <div class="container">
        <?php include 'header.php'; ?>

        <div class="wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header">
                            <h2>Add Certificate</h2>
                        </div>
                        <p>Please fill this form and submit to add certificate</p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group <?php echo (!empty($student_id_err)) ? 'has-error' : ''; ?>">
                                <label>Student Roll No</label>
                                <input type="text" name="student_id" class="form-control" <?php
                                    if (!$_SESSION['is_admin']) {
                                        echo ("disabled = \"true\"");
                                    }
                                    ?>
                                    value="<?php
                                    if (!$_SESSION['is_admin']) {
                                        $student_id = $_SESSION['user_id'];
                                    }
                                     echo $student_id; ?>">
                                <span class="help-block">
                                    <?php echo $student_id_err; ?>
                                </span>
                            </div>
                            <div class="form-group <?php echo (!empty($certificate_name_err)) ? 'has-error' : ''; ?>">
                                <label>Certificate Name</label>
                                <input type="text" name="certificate_name" class="form-control"
                                    value="<?php echo $certificate_name; ?>">
                                <span class="help-block">
                                    <?php echo $certificate_name_err; ?>
                                </span>
                            </div>
                            <div class="form-group <?php echo (!empty($certificate_descs_err)) ? 'has-error' : ''; ?>">
                                <label>Certificate Description</label>
                                <textarea name="certificate_desc"
                                    class="form-control"><?php echo $certificate_desc; ?></textarea>
                                <span class="help-block">
                                    <?php echo $certificate_desc_err; ?>
                                </span>
                            </div>
                            <div class="form-group <?php echo (!empty($certificate_type_err)) ? 'has-error' : ''; ?>">
                                <label>Certificate Type (1 - Co-Curricular 2 - Extra-Curricular)</label>
                                <input type="text" name="certificate_type" class="form-control"
                                    value="<?php echo $certificate_type; ?>">
                                <span class="help-block">
                                    <?php echo $certificate_type_err; ?>
                                </span>
                            </div>
                            <div class="form-group <?php echo (!empty($file_name_err)) ? 'has-error' : ''; ?>">
                                <label>Choose file</label>
                                <input type="file" name="pdf_file" class="form-control" accept=".pdf"
                                    title="Upload PDF" />
                                <span class="help-block">
                                    <?php echo $file_name_err; ?>
                                </span>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <a href="allcertificate.php" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php include 'footer.php'; ?>


    </div> <!-- /container -->
</body>

</html>