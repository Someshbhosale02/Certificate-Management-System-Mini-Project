<?php

// Include config file
require_once 'config.php';
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("location: index.php");
    exit;
}

$certificate_id;

// Define variables and initialize with empty values
$certificate_name = $certificate_desc = $certificate_type = $student_id = $file_name = $file_name = $old_file_name = $file_tmp = "";
$certificate_name_err = $certificate_desc_err = $certificate_type_err = $student_id_err = $file_name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get hidden input value
    if (isset($_POST["certificate_id"])) {
        $certificate_id = $_POST["certificate_id"];
    } else {
        throw new Error("Id not found");
    }

    if (isset($_POST["old_file_name"])) {
        $old_file_name = $_POST["old_file_name"];
    } else {
        throw new Error("old file name not found");
    }

    // Validate name
    $input_certificate_name = trim($_POST["certificate_name"]);
    if (empty($input_certificate_name)) {
        $certificate_name_err = "Please enter a name.";
    } elseif (!filter_var(trim($_POST["certificate_name"]), FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z'-.\s ]+$/")))) {
        $certificate_name_err = 'Please enter a valid certificate name.';
    } else {
        $certificate_name = $input_certificate_name;
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


    $input_file_name = trim($_FILES["pdf_file"]["name"]);
    //validate file name

    if (empty($input_file_name)) {
        $file_name_err = "Please select pdf file.";
    } else {
        $file_name = $input_file_name;
        $file_tmp = $_FILES['pdf_file']['tmp_name'];
    }

    // Validate certificate desc
    $input_certificate_desc = trim($_POST["certificate_desc"]);
    if (empty($input_certificate_desc)) {
        $certificate_desc_err = 'Please enter an certificate desc.';
    } else {
        $certificate_desc = $input_certificate_desc;
    }

    // Validate certificate type
    $input_certificate_type = trim($_POST["certificate_type"]);
    if (empty($input_certificate_type)) {
        $certificate_type_err = "Please enter the certificate type.";
    } elseif (!ctype_digit($input_certificate_type)) {
        $certificate_type_err = 'Please enter a positive certificate type value.';
    } else {
        $certificate_type = $input_certificate_type;
    }

    // Check input errors before inserting in database
    if (empty($certificate_name_err) && empty($student_id_err) && empty($certificate_desc_err) && empty($certificate_type_err)) {
        // Prepare an update statement
        $sql = "UPDATE certificate SET certificate_name=?, certificate_desc=?, certificate_type=?, student_id=?, file_name=? WHERE certificate_id=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param(
                $stmt,
                "sssssi",
                $param_certificate_name,
                $param_certificate_desc,
                $param_certificate_type,
                $param_student_id,
                $param_file_name,
                $param_certificate_id
            );

            // Set parameters
            $param_certificate_name = $certificate_name;
            $param_certificate_desc = $certificate_desc;
            $param_certificate_type = $certificate_type;
            $param_certificate_id = $certificate_id;
            $param_student_id = $student_id;
            
            $param_file_name = $file_name;
            if(empty($file_name)) {
                $param_file_name = $old_file_name;
            }
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                if (!empty($file_name)) {

                    echo ("new file found.\n");
                    if (move_uploaded_file($file_tmp, "./pdf/" . $certificate_id . "-" . $file_name))
                        echo ("new file uploaded\n");

                    echo ("./pdf/" . $certificate_id . "-" . $old_file_name);
                    if (file_exists("./pdf/" . $certificate_id . "-" . $old_file_name)) {
                        echo ("old file found.\n");

                        if (unlink("./pdf/" . $certificate_id . "-" . $old_file_name))
                            echo ("old file deleted.\n");

                    }


                } else {
                    echo ("Keeping old file.\n");
                }
                // Records updated successfully. Redirect to landing page
                header("location: allcertificate.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            //closing statement;
            mysqli_stmt_close($stmt);
        } else {
            echo ("something went wrong");
        }


    } else {
        echo ("some error found");
    }

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["certificate_id"]) && !empty(trim($_GET["certificate_id"]))) {
        // Get URL parameter
        $certificate_id = trim($_GET["certificate_id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM certificate WHERE certificate_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Set parameters
            $param_id = $certificate_id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Retrieve individual field value
                    $certificate_name = $row["certificate_name"];
                    $certificate_desc = $row["certificate_desc"];
                    $certificate_type = $row["certificate_type"];
                    $student_id = $row["student_id"];
                    $old_file_name = $row["file_name"];

                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }

            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

        // Close connection
        mysqli_close($link);
    } else {
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
    <link href="http://localhost/certificatemanagementsystem/css/bootstrap.min.cssnarrow-jumbotron.css"
        rel="stylesheet">
</head>

<body>

    <div class="container">
        <?php include 'header.php'; ?>

        <main role="main">

            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2> Edit Certificate </h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="form-group <?php echo (!empty($student_id_err)) ? 'has-error' : ''; ?>">
                                <label>Student Roll No</label>
                                <input type="text" name="student_id" class="form-control" disabled=<?php echo (!$_SESSION['is_admin']); ?>
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
                        <div class="form-group <?php echo (!empty($certificate_desc_err)) ? 'has-error' : ''; ?>">
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
                        <div class="form-group">
                            <label>Old Certificate</label>
                            <p class="help-block">
                                <?php echo $old_file_name; ?>
                            </p>
                        </div>
                        <div class="form-group <?php echo (!empty($file_name_err)) ? 'has-error' : ''; ?>">
                            <label>Choose new file (optional)</label>
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf" title="Upload PDF" />
                            <span class="help-block">
                                <?php echo $file_name_err; ?>
                            </span>
                        </div>
                        <input type="hidden" name="certificate_id" value="<?php echo $certificate_id; ?>" />
                        <input type="hidden" name="old_file_name" value="<?php echo $old_file_name; ?>" />
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="allcertificate.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>

        </main>


        <?php include 'footer.php'; ?>


    </div> <!-- /container -->
</body>

</html>